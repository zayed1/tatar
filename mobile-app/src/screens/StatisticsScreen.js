import React, { useState, useEffect, useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, ScrollView,
} from 'react-native';
import { apiGetStats } from '../api/client';

const TABS = [
  { key: 'players', label: 'اللاعبين' },
  { key: 'alliances', label: 'التحالفات' },
  { key: 'attack', label: 'هجوم' },
  { key: 'defense', label: 'دفاع' },
  { key: 'heroes', label: 'الأبطال' },
  { key: 'villages', label: 'القرى' },
];

const TRIBE_ICONS = { 1: '🏜️', 2: '🏛️', 3: '🏺', 4: '⚒️', 5: '🐎', 6: '🗡️' };

function RankBadge({ rank }) {
  const colors = { 1: '#ffd700', 2: '#c0c0c0', 3: '#cd7f32' };
  const bg = colors[rank] || '#f0f0f0';
  const textColor = rank <= 3 ? '#333' : '#666';
  return (
    <View style={[styles.rankBadge, { backgroundColor: bg }]}>
      <Text style={[styles.rankNum, { color: textColor }]}>{rank}</Text>
    </View>
  );
}

function PlayerRow({ item, onPress }) {
  return (
    <TouchableOpacity style={styles.row} onPress={() => onPress?.(item.id)}>
      <RankBadge rank={item.rank} />
      <View style={styles.rowContent}>
        <Text style={styles.rowName} numberOfLines={1}>
          {TRIBE_ICONS[item.tribe_id] || ''} {item.name}
        </Text>
        {item.alliance ? <Text style={styles.rowSub}>{item.alliance}</Text> : null}
      </View>
      <View style={styles.rowRight}>
        <Text style={styles.rowScore}>{(item.population || item.points || 0).toLocaleString()}</Text>
        <Text style={styles.rowScoreLabel}>
          {item.population !== undefined ? 'سكان' : 'نقاط'}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

function AllianceRow({ item }) {
  return (
    <View style={styles.row}>
      <RankBadge rank={item.rank} />
      <View style={styles.rowContent}>
        <Text style={styles.rowName} numberOfLines={1}>{item.name}</Text>
        <Text style={styles.rowSub}>{item.members} عضو</Text>
      </View>
      <View style={styles.rowRight}>
        <Text style={styles.rowScore}>{(item.points || 0).toLocaleString()}</Text>
        <Text style={styles.rowScoreLabel}>نقاط</Text>
      </View>
    </View>
  );
}

function HeroRow({ item, onPress }) {
  return (
    <TouchableOpacity style={styles.row} onPress={() => onPress?.(item.id)}>
      <RankBadge rank={item.rank} />
      <View style={styles.rowContent}>
        <Text style={styles.rowName}>🦸 {item.hero_name}</Text>
        <Text style={styles.rowSub}>{item.name}</Text>
      </View>
      <View style={styles.rowRight}>
        <Text style={styles.rowScore}>Lv.{item.level}</Text>
        <Text style={styles.rowScoreLabel}>{item.points} XP</Text>
      </View>
    </TouchableOpacity>
  );
}

function VillageRow({ item }) {
  return (
    <View style={styles.row}>
      <RankBadge rank={item.rank} />
      <View style={styles.rowContent}>
        <Text style={styles.rowName}>{item.name}</Text>
        <Text style={styles.rowSub}>{item.player} ({item.x}|{item.y})</Text>
      </View>
      <View style={styles.rowRight}>
        <Text style={styles.rowScore}>{item.population.toLocaleString()}</Text>
        <Text style={styles.rowScoreLabel}>سكان</Text>
      </View>
    </View>
  );
}

export default function StatisticsScreen({ navigation }) {
  const [tab, setTab] = useState('players');
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(0);
  const [total, setTotal] = useState(0);

  const fetchStats = useCallback(async (p = 0, refresh = false) => {
    if (!refresh) setLoading(true);
    try {
      const data = await apiGetStats(tab, p);
      setItems(p === 0 ? data.items : [...items, ...data.items]);
      setTotal(data.total);
      setPage(p);
    } catch (e) {
      // silent
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [tab]);

  useEffect(() => {
    setItems([]);
    fetchStats(0);
  }, [tab]);

  const openProfile = (uid) => {
    navigation.navigate('WebPage', { path: `profile?uid=${uid}` });
  };

  const renderItem = ({ item }) => {
    if (tab === 'alliances') return <AllianceRow item={item} />;
    if (tab === 'heroes') return <HeroRow item={item} onPress={openProfile} />;
    if (tab === 'villages') return <VillageRow item={item} />;
    return <PlayerRow item={item} onPress={openProfile} />;
  };

  return (
    <View style={styles.container}>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.tabBar} contentContainerStyle={styles.tabBarContent}>
        {TABS.map((t) => (
          <TouchableOpacity key={t.key} style={[styles.tabChip, tab === t.key && styles.tabChipActive]} onPress={() => setTab(t.key)}>
            <Text style={[styles.tabChipText, tab === t.key && styles.tabChipTextActive]}>{t.label}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {loading && items.length === 0 ? (
        <ActivityIndicator size="large" color="#c0392b" style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item, idx) => `${tab}-${item.id || idx}`}
          renderItem={renderItem}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchStats(0, true); }} />}
          onEndReached={() => { if (items.length < total) fetchStats(page + 1); }}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={<Text style={styles.empty}>لا توجد بيانات</Text>}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  tabBar: { backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee', maxHeight: 50 },
  tabBarContent: { paddingHorizontal: 8, alignItems: 'center', gap: 6, flexDirection: 'row-reverse' },
  tabChip: { paddingHorizontal: 14, paddingVertical: 8, borderRadius: 20, backgroundColor: '#f0f0f0' },
  tabChipActive: { backgroundColor: '#c0392b' },
  tabChipText: { fontSize: 12, fontWeight: '700', color: '#555' },
  tabChipTextActive: { color: '#fff' },
  row: {
    flexDirection: 'row-reverse', padding: 12, backgroundColor: '#fff',
    borderBottomWidth: 1, borderBottomColor: '#f0f0f0', alignItems: 'center',
  },
  rankBadge: {
    width: 32, height: 32, borderRadius: 16, alignItems: 'center',
    justifyContent: 'center', marginLeft: 10,
  },
  rankNum: { fontSize: 13, fontWeight: '900' },
  rowContent: { flex: 1, alignItems: 'flex-end' },
  rowName: { fontSize: 14, fontWeight: '700', color: '#333' },
  rowSub: { fontSize: 11, color: '#888', marginTop: 1 },
  rowRight: { alignItems: 'flex-start', marginRight: 10 },
  rowScore: { fontSize: 14, fontWeight: '800', color: '#c0392b' },
  rowScoreLabel: { fontSize: 10, color: '#aaa' },
  empty: { textAlign: 'center', color: '#999', marginTop: 40, fontSize: 14 },
});
