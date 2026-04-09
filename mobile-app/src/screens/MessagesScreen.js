import React, { useState, useEffect, useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, TextInput, Alert, KeyboardAvoidingView, Platform,
} from 'react-native';
import { apiGetMessages, apiReadMessage, apiSendMessage, apiDeleteMessage } from '../api/client';

const TABS = [
  { key: 'inbox', label: 'الوارد' },
  { key: 'sent', label: 'المرسل' },
  { key: 'compose', label: '✏️ إرسال' },
];

export default function MessagesScreen() {
  const [tab, setTab] = useState('inbox');
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedMsg, setSelectedMsg] = useState(null);
  const [page, setPage] = useState(0);
  const [total, setTotal] = useState(0);

  // Compose state
  const [toName, setToName] = useState('');
  const [subject, setSubject] = useState('');
  const [body, setBody] = useState('');
  const [sending, setSending] = useState(false);

  const fetchMessages = useCallback(async (p = 0, refresh = false) => {
    if (tab === 'compose') return;
    if (!refresh) setLoading(true);
    try {
      const data = await apiGetMessages(tab, p);
      setMessages(p === 0 ? data.messages : [...messages, ...data.messages]);
      setTotal(data.total);
      setPage(p);
    } catch (e) {
      Alert.alert('خطأ', e.message);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [tab]);

  useEffect(() => {
    if (tab !== 'compose') {
      setSelectedMsg(null);
      setMessages([]);
      fetchMessages(0);
    }
  }, [tab, fetchMessages]);

  const handleRefresh = () => {
    setRefreshing(true);
    fetchMessages(0, true);
  };

  const handleLoadMore = () => {
    if (messages.length < total) {
      fetchMessages(page + 1);
    }
  };

  const handleOpenMessage = async (msg) => {
    try {
      const full = await apiReadMessage(msg.id);
      setSelectedMsg(full);
    } catch (e) {
      Alert.alert('خطأ', e.message);
    }
  };

  const handleDelete = (id) => {
    Alert.alert('حذف', 'هل تريد حذف هذه الرسالة؟', [
      { text: 'إلغاء', style: 'cancel' },
      {
        text: 'حذف', style: 'destructive', onPress: async () => {
          try {
            await apiDeleteMessage(id);
            setSelectedMsg(null);
            fetchMessages(0);
          } catch (e) { Alert.alert('خطأ', e.message); }
        }
      },
    ]);
  };

  const handleReply = () => {
    if (!selectedMsg) return;
    setToName(selectedMsg.from_name);
    setSubject('رد: ' + selectedMsg.title);
    setBody('');
    setSelectedMsg(null);
    setTab('compose');
  };

  const handleSend = async () => {
    if (!toName.trim() || !subject.trim() || !body.trim()) {
      Alert.alert('خطأ', 'جميع الحقول مطلوبة');
      return;
    }
    setSending(true);
    try {
      await apiSendMessage(toName.trim(), subject.trim(), body.trim());
      Alert.alert('تم', 'تم إرسال الرسالة بنجاح');
      setToName(''); setSubject(''); setBody('');
      setTab('inbox');
    } catch (e) {
      Alert.alert('خطأ', e.message);
    } finally {
      setSending(false);
    }
  };

  // Message detail view
  if (selectedMsg) {
    return (
      <View style={styles.container}>
        <View style={styles.detailHeader}>
          <TouchableOpacity onPress={() => setSelectedMsg(null)} style={styles.backTouchable}>
            <Text style={styles.backButton}>رجوع ←</Text>
          </TouchableOpacity>
          <View style={styles.detailActions}>
            <TouchableOpacity onPress={handleReply} style={styles.actionBtn}>
              <Text style={styles.actionBtnText}>رد ↩</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={() => handleDelete(selectedMsg.id)} style={[styles.actionBtn, styles.deleteBtn]}>
              <Text style={styles.actionBtnText}>🗑</Text>
            </TouchableOpacity>
          </View>
        </View>
        <View style={styles.detailCard}>
          <Text style={styles.detailTitle}>{selectedMsg.title}</Text>
          <Text style={styles.detailMeta}>
            من: {selectedMsg.from_name}  →  إلى: {selectedMsg.to_name}
          </Text>
          <Text style={styles.detailDate}>{selectedMsg.date}</Text>
          <View style={styles.divider} />
          <Text style={styles.detailBody}>{selectedMsg.body}</Text>
        </View>
      </View>
    );
  }

  // Compose view
  if (tab === 'compose') {
    return (
      <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
        <View style={styles.tabBar}>
          {TABS.map((t) => (
            <TouchableOpacity key={t.key} style={[styles.tab, tab === t.key && styles.tabActive]} onPress={() => setTab(t.key)}>
              <Text style={[styles.tabText, tab === t.key && styles.tabTextActive]}>{t.label}</Text>
            </TouchableOpacity>
          ))}
        </View>
        <View style={styles.composeForm}>
          <TextInput style={styles.input} placeholder="إلى (اسم اللاعب)" placeholderTextColor="#888" value={toName} onChangeText={setToName} textAlign="right" />
          <TextInput style={styles.input} placeholder="الموضوع" placeholderTextColor="#888" value={subject} onChangeText={setSubject} textAlign="right" />
          <TextInput style={[styles.input, styles.bodyInput]} placeholder="الرسالة" placeholderTextColor="#888" value={body} onChangeText={setBody} textAlign="right" multiline />
          <TouchableOpacity style={[styles.sendButton, sending && { opacity: 0.6 }]} onPress={handleSend} disabled={sending}>
            {sending ? <ActivityIndicator color="#fff" /> : <Text style={styles.sendButtonText}>إرسال</Text>}
          </TouchableOpacity>
        </View>
      </KeyboardAvoidingView>
    );
  }

  // Message list view
  return (
    <View style={styles.container}>
      <View style={styles.tabBar}>
        {TABS.map((t) => (
          <TouchableOpacity key={t.key} style={[styles.tab, tab === t.key && styles.tabActive]} onPress={() => setTab(t.key)}>
            <Text style={[styles.tabText, tab === t.key && styles.tabTextActive]}>{t.label}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {loading && messages.length === 0 ? (
        <ActivityIndicator size="large" color="#c0392b" style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={messages}
          keyExtractor={(item) => String(item.id)}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} />}
          onEndReached={handleLoadMore}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={<Text style={styles.empty}>لا توجد رسائل</Text>}
          renderItem={({ item }) => (
            <TouchableOpacity style={[styles.msgRow, !item.is_read && styles.msgUnread]} onPress={() => handleOpenMessage(item)}>
              <View style={styles.msgContent}>
                <Text style={[styles.msgName, !item.is_read && styles.bold]} numberOfLines={1}>
                  {tab === 'inbox' ? item.from_name : item.to_name}
                </Text>
                <Text style={[styles.msgTitle, !item.is_read && styles.bold]} numberOfLines={1}>{item.title}</Text>
              </View>
              <Text style={styles.msgDate}>{item.date}</Text>
            </TouchableOpacity>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6', paddingBottom: 60 },
  backTouchable: { minWidth: 60, minHeight: 44, justifyContent: 'center' },
  tabBar: { flexDirection: 'row-reverse', backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee' },
  tab: { flex: 1, paddingVertical: 12, alignItems: 'center' },
  tabActive: { borderBottomWidth: 3, borderBottomColor: '#c0392b' },
  tabText: { fontSize: 13, fontWeight: '600', color: '#888' },
  tabTextActive: { color: '#c0392b' },
  msgRow: { flexDirection: 'row-reverse', padding: 14, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#f0f0f0', alignItems: 'center' },
  msgUnread: { backgroundColor: '#fef9f0' },
  msgContent: { flex: 1, alignItems: 'flex-end' },
  msgName: { fontSize: 13, color: '#c0392b', marginBottom: 2 },
  msgTitle: { fontSize: 14, color: '#333' },
  msgDate: { fontSize: 11, color: '#999', marginLeft: 10 },
  bold: { fontWeight: '800' },
  empty: { textAlign: 'center', color: '#999', marginTop: 40, fontSize: 14 },
  // Detail
  detailHeader: { flexDirection: 'row-reverse', justifyContent: 'space-between', alignItems: 'center', padding: 12, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee' },
  backButton: { fontSize: 15, color: '#c0392b', fontWeight: '700' },
  detailActions: { flexDirection: 'row', gap: 8 },
  actionBtn: { backgroundColor: '#f0f0f0', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6 },
  deleteBtn: { backgroundColor: '#fee' },
  actionBtnText: { fontSize: 13, fontWeight: '700' },
  detailCard: { margin: 12, backgroundColor: '#fff', borderRadius: 10, padding: 16, borderWidth: 1, borderColor: '#eee' },
  detailTitle: { fontSize: 17, fontWeight: '800', color: '#333', textAlign: 'right', marginBottom: 6 },
  detailMeta: { fontSize: 12, color: '#888', textAlign: 'right', marginBottom: 2 },
  detailDate: { fontSize: 11, color: '#aaa', textAlign: 'right' },
  divider: { height: 1, backgroundColor: '#eee', marginVertical: 12 },
  detailBody: { fontSize: 15, color: '#333', textAlign: 'right', lineHeight: 24 },
  // Compose
  composeForm: { padding: 16 },
  input: { backgroundColor: '#fff', borderWidth: 1, borderColor: '#ddd', borderRadius: 8, padding: 12, fontSize: 15, marginBottom: 10, color: '#333' },
  bodyInput: { height: 150, textAlignVertical: 'top' },
  sendButton: { backgroundColor: '#c0392b', borderRadius: 8, padding: 14, alignItems: 'center' },
  sendButtonText: { color: '#fff', fontSize: 16, fontWeight: '800' },
});
