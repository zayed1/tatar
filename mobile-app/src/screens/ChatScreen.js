import React, { useState, useEffect, useRef, useCallback } from 'react';
import {
  View, Text, FlatList, TextInput, TouchableOpacity,
  StyleSheet, KeyboardAvoidingView, Platform, AppState,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { apiChatGet, apiChatSend, apiAllianceChatGet, apiAllianceChatSend } from '../api/client';

const TABS = [
  { key: 'global', label: '💬 العام' },
  { key: 'alliance', label: '⚔️ التحالف' },
];

function ChatMessage({ item, isOwn }) {
  return (
    <View style={[styles.msgBubble, isOwn && styles.msgBubbleOwn]}>
      <View style={styles.msgHeader}>
        <Text style={[styles.msgName, { color: item.color }]}>{item.name}</Text>
        <Text style={styles.msgTime}>{item.time}</Text>
      </View>
      <Text style={styles.msgText}>{item.text}</Text>
    </View>
  );
}

export default function ChatScreen() {
  const [tab, setTab] = useState('global');
  const [messages, setMessages] = useState([]);
  const [text, setText] = useState('');
  const [sending, setSending] = useState(false);
  const [myId, setMyId] = useState(0);
  const flatListRef = useRef(null);
  const pollRef = useRef(null);

  const fetchMessages = useCallback(async () => {
    try {
      const data = tab === 'global' ? await apiChatGet() : await apiAllianceChatGet();
      setMessages(data.messages);
    } catch (e) {
      // Alliance chat may fail if not in alliance
    }
  }, [tab]);

  useEffect(() => {
    AsyncStorage.getItem('@tatar_auth').then((auth) => {
      if (auth) {
        const { player } = JSON.parse(auth);
        setMyId(player.id);
      }
    });
  }, []);

  useEffect(() => {
    setMessages([]);
    fetchMessages();
    pollRef.current = setInterval(fetchMessages, 3000);

    // Pause polling when app goes to background
    const sub = AppState.addEventListener('change', (state) => {
      if (state === 'active') {
        fetchMessages();
        pollRef.current = setInterval(fetchMessages, 3000);
      } else {
        clearInterval(pollRef.current);
      }
    });

    return () => {
      clearInterval(pollRef.current);
      sub.remove();
    };
  }, [tab, fetchMessages]);

  const handleSend = async () => {
    const msg = text.trim();
    if (!msg || sending) return;
    setSending(true);
    setText('');
    try {
      if (tab === 'global') {
        await apiChatSend(msg);
      } else {
        await apiAllianceChatSend(msg);
      }
      await fetchMessages();
    } catch (e) {
      setText(msg); // restore on error
    } finally {
      setSending(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={90}
    >
      {/* Tabs */}
      <View style={styles.tabBar}>
        {TABS.map((t) => (
          <TouchableOpacity key={t.key} style={[styles.tab, tab === t.key && styles.tabActive]} onPress={() => setTab(t.key)}>
            <Text style={[styles.tabText, tab === t.key && styles.tabTextActive]}>{t.label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Messages */}
      <FlatList
        ref={flatListRef}
        data={messages}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => <ChatMessage item={item} isOwn={item.user_id === myId} />}
        contentContainerStyle={styles.messagesList}
        onContentSizeChange={() => flatListRef.current?.scrollToEnd({ animated: false })}
        ListEmptyComponent={<Text style={styles.empty}>لا توجد رسائل بعد</Text>}
      />

      {/* Input */}
      <View style={styles.inputBar}>
        <TouchableOpacity
          style={[styles.sendBtn, (!text.trim() || sending) && styles.sendBtnDisabled]}
          onPress={handleSend}
          disabled={!text.trim() || sending}
        >
          <Text style={styles.sendBtnText}>إرسال</Text>
        </TouchableOpacity>
        <TextInput
          style={styles.input}
          placeholder="اكتب رسالة..."
          placeholderTextColor="#888"
          value={text}
          onChangeText={setText}
          textAlign="right"
          maxLength={250}
          returnKeyType="send"
          onSubmitEditing={handleSend}
        />
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6', paddingBottom: 60 },
  tabBar: { flexDirection: 'row-reverse', backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee' },
  tab: { flex: 1, paddingVertical: 12, alignItems: 'center' },
  tabActive: { borderBottomWidth: 3, borderBottomColor: '#c0392b' },
  tabText: { fontSize: 13, fontWeight: '600', color: '#888' },
  tabTextActive: { color: '#c0392b' },
  messagesList: { padding: 8, paddingBottom: 4 },
  msgBubble: {
    backgroundColor: '#fff', borderRadius: 10, padding: 10,
    marginBottom: 6, borderWidth: 1, borderColor: '#eee', maxWidth: '85%',
    alignSelf: 'flex-end',
  },
  msgBubbleOwn: { backgroundColor: '#e8f5e9', borderColor: '#c8e6c9' },
  msgHeader: { flexDirection: 'row-reverse', justifyContent: 'space-between', marginBottom: 4 },
  msgName: { fontSize: 12, fontWeight: '800' },
  msgTime: { fontSize: 10, color: '#aaa' },
  msgText: { fontSize: 14, color: '#333', textAlign: 'right', lineHeight: 20 },
  empty: { textAlign: 'center', color: '#999', marginTop: 40, fontSize: 14 },
  inputBar: {
    flexDirection: 'row-reverse', padding: 8, backgroundColor: '#fff',
    borderTopWidth: 1, borderTopColor: '#eee', gap: 8,
  },
  input: {
    flex: 1, backgroundColor: '#f5f5f5', borderRadius: 20, paddingHorizontal: 16,
    paddingVertical: 10, fontSize: 15, color: '#333',
  },
  sendBtn: {
    backgroundColor: '#c0392b', borderRadius: 20, paddingHorizontal: 18,
    justifyContent: 'center',
  },
  sendBtnDisabled: { opacity: 0.4 },
  sendBtnText: { color: '#fff', fontSize: 14, fontWeight: '800' },
});
