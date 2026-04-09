import React, { useState, useEffect } from 'react';
import {
  View, Text, FlatList, StyleSheet, ActivityIndicator,
  RefreshControl, TouchableOpacity, Alert,
} from 'react-native';
import { apiGetTroops } from '../api/client';

const TROOP_ICONS = ['', '🗡️','⚔️','🏹','🐴','🐎','🪓','💣','👑','🏕️','🦸'];

function TroopItem({ item, onPress }) {
  const iconIdx = ((item.id - 1) % 10) + 1;
  return (
    <TouchableOpacity style={styles.troopRow} onPress={() => onPress?.(item)} disabled={!item.researched}>
      <Text style={styles.troopIcon}>{TROOP_ICONS[iconIdx] || '⚔️'}</Text>
      <View style={styles.troopInfo}>
        <Text style={[styles.troopName, !item.researched && styles.troopLocked]}>{item.name}</Text>
        {!item.researched && <Text style={styles.lockedText}>🔒 يحتاج بحث</Text>}
      </View>
      <View style={styles.countBadge}>
        <Text style={styles.countText}>{item.count.toLocaleString()}</Text>
      </View>
    </TouchableOpacity>
  );
}

function QueueItem({ item }) {
  const mins = Math.floor(item.remaining_sec / 60);
  const secs = item.remaining_sec % 60;
  return (
    <View style={styles.queueRow}>
      <Text style={styles.queueName}>⚔️ {item.troop_name} × {item.count}</Text>
      <Text style={styles.queueTime}>
        {mins > 0 ? `${mins}د ${secs}ث` : `${secs}ث`}
      </Text>
    </View>
  );
}

export default function TroopsScreen({ navigation }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchData = async (refresh = false) => {
    if (!refresh) setLoading(true);
    try {
      const d = await apiGetTroops();
      setData(d);
    } catch (e) {
      Alert.alert('خطأ', e.message);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => { fetchData(); }, []);

  if (loading) return <ActivityIndicator size="large" color="#c0392b" style={{ flex: 1, marginTop: 60 }} />;
  if (!data) return null;

  const totalTroops = data.troops.reduce((sum, t) => sum + t.count, 0);

  const openTrainPage = (item) => {
    // Open barracks in WebView for actual training
    navigation.navigate('WebPage', { path: 'build.php?bid=15' });
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.villageName}>⚔️ {data.village_name}</Text>
        <Text style={styles.totalTroops}>إجمالي: {totalTroops.toLocaleString()} جندي</Text>
      </View>

      {data.training_queue.length > 0 && (
        <View style={styles.queueCard}>
          <Text style={styles.queueTitle}>⏳ قيد التدريب</Text>
          {data.training_queue.map((q, i) => <QueueItem key={i} item={q} />)}
        </View>
      )}

      <FlatList
        data={data.troops}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => <TroopItem item={item} onPress={openTrainPage} />}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchData(true); }} />
        }
        contentContainerStyle={styles.list}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  header: { backgroundColor: '#fff', padding: 12, borderBottomWidth: 1, borderBottomColor: '#eee', alignItems: 'center' },
  villageName: { fontSize: 16, fontWeight: '800', color: '#333' },
  totalTroops: { fontSize: 12, color: '#888', marginTop: 2 },
  queueCard: { margin: 10, backgroundColor: '#e8f5e9', borderRadius: 10, padding: 12, borderWidth: 1, borderColor: '#a5d6a7' },
  queueTitle: { fontSize: 13, fontWeight: '800', color: '#2e7d32', marginBottom: 6 },
  queueRow: { flexDirection: 'row-reverse', justifyContent: 'space-between', paddingVertical: 4 },
  queueName: { fontSize: 13, color: '#333', fontWeight: '600' },
  queueTime: { fontSize: 13, color: '#c0392b', fontWeight: '700' },
  list: { paddingHorizontal: 10, paddingBottom: 20 },
  troopRow: { flexDirection: 'row-reverse', alignItems: 'center', backgroundColor: '#fff', borderRadius: 10, padding: 12, marginTop: 6, borderWidth: 1, borderColor: '#eee' },
  troopIcon: { fontSize: 22, marginLeft: 10 },
  troopInfo: { flex: 1, alignItems: 'flex-end' },
  troopName: { fontSize: 14, fontWeight: '700', color: '#333' },
  troopLocked: { color: '#aaa' },
  lockedText: { fontSize: 11, color: '#e67e22', marginTop: 2 },
  countBadge: { backgroundColor: '#f0f0f0', borderRadius: 12, paddingHorizontal: 12, paddingVertical: 4, minWidth: 50, alignItems: 'center' },
  countText: { fontSize: 14, fontWeight: '800', color: '#333' },
});
