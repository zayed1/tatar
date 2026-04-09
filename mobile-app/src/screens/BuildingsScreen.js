import React, { useState, useEffect } from 'react';
import {
  View, Text, FlatList, StyleSheet, ActivityIndicator,
  RefreshControl, TouchableOpacity, Alert,
} from 'react-native';
import { apiGetBuildings } from '../api/client';

const BUILDING_ICONS = {
  1:'🪵',2:'🧱',3:'⛏️',4:'🌾',5:'🪚',6:'🏭',7:'🔥',8:'🌾',9:'🍞',
  10:'📦',11:'🏪',12:'⚒️',13:'🗡️',14:'🏟️',15:'⚔️',16:'🐴',17:'🏪',
  18:'🏛️',19:'⚔️',20:'🐴',21:'🧱',22:'🧱',23:'🧱',24:'🏰',25:'👑',
  26:'🏛️',27:'📚',28:'📋',29:'🛒',30:'🕳️',31:'🪤',32:'🦸',33:'🏥',
  34:'🌟',35:'🏰',36:'⛺',37:'💧',38:'🐎',39:'🏺',40:'🗺️',
};

function BuildingItem({ item, onPress }) {
  const icon = BUILDING_ICONS[item.id] || '🏠';
  return (
    <TouchableOpacity style={styles.buildingRow} onPress={() => onPress?.(item)}>
      <Text style={styles.buildingIcon}>{icon}</Text>
      <View style={styles.buildingInfo}>
        <Text style={styles.buildingName}>{item.name}</Text>
        <Text style={styles.buildingSlot}>خانة {item.slot}</Text>
      </View>
      <View style={styles.levelBadge}>
        <Text style={styles.levelText}>Lv.{item.level}</Text>
      </View>
    </TouchableOpacity>
  );
}

function QueueItem({ item }) {
  const mins = Math.floor(item.remaining_sec / 60);
  const secs = item.remaining_sec % 60;
  return (
    <View style={styles.queueRow}>
      <Text style={styles.queueType}>
        {item.type === 'upgrade' ? '🔨 بناء' : '💥 هدم'}
      </Text>
      <Text style={styles.queueTime}>
        {mins > 0 ? `${mins}د ${secs}ث` : `${secs}ث`}
      </Text>
    </View>
  );
}

export default function BuildingsScreen({ navigation }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchData = async (refresh = false) => {
    if (!refresh) setLoading(true);
    try {
      const d = await apiGetBuildings();
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

  const openBuildPage = (item) => {
    navigation.navigate('WebPage', { path: `build.php?bid=${item.slot}` });
  };

  return (
    <View style={styles.container}>
      <Text style={styles.villageName}>🏘️ {data.village_name}</Text>

      {data.queue.length > 0 && (
        <View style={styles.queueCard}>
          <Text style={styles.queueTitle}>⏳ قائمة الانتظار</Text>
          {data.queue.map((q) => <QueueItem key={q.id} item={q} />)}
        </View>
      )}

      <FlatList
        data={data.buildings}
        keyExtractor={(item) => String(item.slot)}
        renderItem={({ item }) => <BuildingItem item={item} onPress={openBuildPage} />}
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
  villageName: { fontSize: 16, fontWeight: '800', color: '#333', textAlign: 'center', paddingVertical: 10, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee' },
  queueCard: { margin: 10, backgroundColor: '#fff8e1', borderRadius: 10, padding: 12, borderWidth: 1, borderColor: '#ffe082' },
  queueTitle: { fontSize: 13, fontWeight: '800', color: '#f57f17', marginBottom: 6 },
  queueRow: { flexDirection: 'row-reverse', justifyContent: 'space-between', paddingVertical: 4 },
  queueType: { fontSize: 13, color: '#333', fontWeight: '600' },
  queueTime: { fontSize: 13, color: '#c0392b', fontWeight: '700' },
  list: { paddingHorizontal: 10, paddingBottom: 20 },
  buildingRow: { flexDirection: 'row-reverse', alignItems: 'center', backgroundColor: '#fff', borderRadius: 10, padding: 12, marginTop: 6, borderWidth: 1, borderColor: '#eee' },
  buildingIcon: { fontSize: 24, marginLeft: 10 },
  buildingInfo: { flex: 1, alignItems: 'flex-end' },
  buildingName: { fontSize: 14, fontWeight: '700', color: '#333' },
  buildingSlot: { fontSize: 11, color: '#aaa' },
  levelBadge: { backgroundColor: '#c0392b', borderRadius: 12, paddingHorizontal: 10, paddingVertical: 4 },
  levelText: { color: '#fff', fontSize: 12, fontWeight: '800' },
});
