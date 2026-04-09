import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, StyleSheet, ActivityIndicator, Alert,
} from 'react-native';
import { apiGetProfile } from '../api/client';

const TRIBES = { 1: '🏜️ العرب', 2: '🏛️ الرومان', 3: '🏺 اليونان', 4: '⚒️ الجرمان', 5: '🐎 المغول', 6: '🗡️ الفرس' };

function StatRow({ label, value }) {
  return (
    <View style={styles.statRow}>
      <Text style={styles.statValue}>{typeof value === 'number' ? value.toLocaleString() : value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

export default function ProfileScreen({ route }) {
  const uid = route?.params?.uid;
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const d = await apiGetProfile(uid);
        setData(d);
      } catch (e) {
        Alert.alert('خطأ', e.message);
      } finally {
        setLoading(false);
      }
    })();
  }, [uid]);

  if (loading) return <ActivityIndicator size="large" color="#c0392b" style={{ flex: 1, marginTop: 60 }} />;
  if (!data) return <Text style={styles.empty}>لم يتم العثور على اللاعب</Text>;

  const online = data.hours_ago <= 1;

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Header Card */}
      <View style={styles.headerCard}>
        <View style={styles.nameRow}>
          <Text style={styles.playerName}>{data.name}</Text>
          <View style={[styles.onlineDot, online && styles.onlineDotActive]} />
        </View>
        <Text style={styles.tribe}>{TRIBES[data.tribe_id] || 'غير معروف'}</Text>
        {data.alliance && <Text style={styles.alliance}>⚔️ {data.alliance}</Text>}
        <View style={styles.rankBadge}>
          <Text style={styles.rankText}>المرتبة #{data.rank}</Text>
        </View>
      </View>

      {/* Stats Grid */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>الإحصائيات</Text>
        <View style={styles.statsGrid}>
          <StatRow label="السكان" value={data.population} />
          <StatRow label="القرى" value={data.villages_count} />
          <StatRow label="نقاط هجوم" value={data.points.attack} />
          <StatRow label="نقاط دفاع" value={data.points.defense} />
          <StatRow label="هجوم الأسبوع" value={data.points.week_attack} />
          <StatRow label="دفاع الأسبوع" value={data.points.week_defense} />
        </View>
      </View>

      {/* Hero */}
      {data.hero && (
        <View style={styles.card}>
          <Text style={styles.cardTitle}>🦸 البطل</Text>
          <StatRow label="الاسم" value={data.hero.name} />
          <StatRow label="المستوى" value={data.hero.level} />
          <StatRow label="النقاط" value={data.hero.points} />
        </View>
      )}

      {/* Villages */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>🏘️ القرى ({data.villages.length})</Text>
        {data.villages.map((v) => (
          <View key={v.id} style={styles.villageRow}>
            <View style={styles.villageInfo}>
              <Text style={styles.villageName}>
                {v.is_capital ? '👑 ' : ''}{v.name}
              </Text>
              <Text style={styles.villageCoords}>({v.x}|{v.y})</Text>
            </View>
            <Text style={styles.villagePop}>{v.population.toLocaleString()} 👤</Text>
          </View>
        ))}
      </View>

      {/* Info */}
      {data.description ? (
        <View style={styles.card}>
          <Text style={styles.cardTitle}>📝 الوصف</Text>
          <Text style={styles.description}>{data.description}</Text>
        </View>
      ) : null}

      <Text style={styles.registered}>📅 مسجل منذ {data.registered}</Text>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  content: { padding: 12, paddingBottom: 40 },
  empty: { textAlign: 'center', color: '#999', marginTop: 60, fontSize: 14 },
  headerCard: {
    backgroundColor: '#1a1a2e', borderRadius: 14, padding: 20,
    alignItems: 'center', marginBottom: 12,
  },
  nameRow: { flexDirection: 'row-reverse', alignItems: 'center', gap: 8 },
  playerName: { fontSize: 22, fontWeight: '900', color: '#ffd700' },
  onlineDot: { width: 10, height: 10, borderRadius: 5, backgroundColor: '#666' },
  onlineDotActive: { backgroundColor: '#27ae60' },
  tribe: { fontSize: 14, color: '#aaa', marginTop: 4 },
  alliance: { fontSize: 13, color: '#87ceeb', marginTop: 4 },
  rankBadge: {
    backgroundColor: '#c0392b', borderRadius: 12, paddingHorizontal: 14,
    paddingVertical: 4, marginTop: 10,
  },
  rankText: { color: '#fff', fontSize: 13, fontWeight: '800' },
  card: {
    backgroundColor: '#fff', borderRadius: 10, padding: 14,
    marginBottom: 10, borderWidth: 1, borderColor: '#eee',
  },
  cardTitle: {
    fontSize: 15, fontWeight: '800', color: '#333',
    textAlign: 'right', marginBottom: 10, borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0', paddingBottom: 8,
  },
  statsGrid: { gap: 2 },
  statRow: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    paddingVertical: 6, borderBottomWidth: 1, borderBottomColor: '#f8f8f8',
  },
  statLabel: { fontSize: 13, color: '#888' },
  statValue: { fontSize: 13, fontWeight: '700', color: '#333' },
  villageRow: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    alignItems: 'center', paddingVertical: 8,
    borderBottomWidth: 1, borderBottomColor: '#f8f8f8',
  },
  villageInfo: { alignItems: 'flex-end' },
  villageName: { fontSize: 13, fontWeight: '700', color: '#333' },
  villageCoords: { fontSize: 11, color: '#aaa' },
  villagePop: { fontSize: 12, fontWeight: '600', color: '#666' },
  description: { fontSize: 13, color: '#555', textAlign: 'right', lineHeight: 22 },
  registered: { textAlign: 'center', color: '#aaa', fontSize: 11, marginTop: 8 },
});
