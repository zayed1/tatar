import AsyncStorage from '@react-native-async-storage/async-storage';

const BASE_URL = 'https://tatar-production.up.railway.app';
const TOKEN_KEY = '@tatar_token';
const AUTH_KEY = '@tatar_auth';

export const API_BASE = BASE_URL;

export async function saveAuth(token, player, villages) {
  await AsyncStorage.setItem(TOKEN_KEY, token);
  await AsyncStorage.setItem(AUTH_KEY, JSON.stringify({ player, villages }));
}

export async function getToken() {
  return AsyncStorage.getItem(TOKEN_KEY);
}

export async function getStoredAuth() {
  const auth = await AsyncStorage.getItem(AUTH_KEY);
  return auth ? JSON.parse(auth) : null;
}

export async function clearAuth() {
  await AsyncStorage.multiRemove([TOKEN_KEY, AUTH_KEY]);
}

export async function apiLogin(name, password) {
  const res = await fetch(`${BASE_URL}/api/login.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, password }),
  });
  const json = await res.json();
  if (!json.ok) throw new Error(json.error || 'Login failed');
  return json.data;
}

async function authFetch(url, options = {}) {
  const token = await getToken();
  if (!token) throw new Error('Not authenticated');
  const res = await fetch(url, {
    ...options,
    headers: { Authorization: `Bearer ${token}`, ...options.headers },
  });
  const json = await res.json();
  if (!json.ok) throw new Error(json.error || 'Request failed');
  return json.data;
}

export async function apiGetData() {
  return authFetch(`${BASE_URL}/api/data.php`);
}

// Messages
export async function apiGetMessages(folder = 'inbox', page = 0) {
  return authFetch(`${BASE_URL}/api/messages.php?action=${folder}&page=${page}&limit=30`);
}

export async function apiReadMessage(id) {
  return authFetch(`${BASE_URL}/api/messages.php?action=read&id=${id}`);
}

export async function apiSendMessage(to, title, body) {
  return authFetch(`${BASE_URL}/api/messages.php?action=send`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ to, title, body }),
  });
}

export async function apiDeleteMessage(id) {
  return authFetch(`${BASE_URL}/api/messages.php?action=delete`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id }),
  });
}

// Reports
export async function apiGetReports(cat = 0, page = 0) {
  return authFetch(`${BASE_URL}/api/reports.php?action=list&cat=${cat}&page=${page}&limit=20`);
}

export async function apiReadReport(id) {
  return authFetch(`${BASE_URL}/api/reports.php?action=read&id=${id}`);
}

export async function apiDeleteReport(id) {
  return authFetch(`${BASE_URL}/api/reports.php?action=delete`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id }),
  });
}

// Profile
export async function apiGetProfile(uid) {
  const param = uid ? `&uid=${uid}` : '';
  return authFetch(`${BASE_URL}/api/profile.php?action=view${param}`);
}

// Statistics
export async function apiGetStats(tab = 'players', page = 0) {
  return authFetch(`${BASE_URL}/api/statistics.php?tab=${tab}&page=${page}&limit=20`);
}

// Chat
export async function apiChatGet() {
  return authFetch(`${BASE_URL}/api/chat.php?action=get`);
}

export async function apiChatSend(text) {
  return authFetch(`${BASE_URL}/api/chat.php?action=send`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ text }),
  });
}

export async function apiAllianceChatGet() {
  return authFetch(`${BASE_URL}/api/chat.php?action=alliance`);
}

export async function apiAllianceChatSend(text) {
  return authFetch(`${BASE_URL}/api/chat.php?action=alliance_send`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ text }),
  });
}

// Alliance
export async function apiGetAlliance(id) {
  const param = id ? `&id=${id}` : '';
  return authFetch(`${BASE_URL}/api/alliance.php?action=info${param}`);
}

// Buildings & Troops
export async function apiGetBuildings(vid) {
  const param = vid ? `&vid=${vid}` : '';
  return authFetch(`${BASE_URL}/api/build.php?action=buildings${param}`);
}

export async function apiGetTroops(vid) {
  const param = vid ? `&vid=${vid}` : '';
  return authFetch(`${BASE_URL}/api/build.php?action=troops${param}`);
}
