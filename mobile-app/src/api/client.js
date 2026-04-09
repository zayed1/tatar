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

export async function apiGetData() {
  const token = await getToken();
  if (!token) throw new Error('Not authenticated');
  const res = await fetch(`${BASE_URL}/api/data.php`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  const json = await res.json();
  if (!json.ok) throw new Error(json.error || 'Failed to load data');
  return json.data;
}
