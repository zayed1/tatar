import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, Alert,
} from 'react-native';
import { apiGetAlliance } from '../api/client';

function OnlineDot({ hoursAgo }) {
  let color = '#ccc';
  if (hoursAgo <= 1) color = '#27ae60';
  else if (hoursAgo <= 24) color = '#f39c12';
  else if (hoursAgo <= 72) color = '#e67e22';
  else color = '#ccc';
  return <View style={[styles.dot, { backgroundColor: color }]} />;
}

function StatBox({ label, value }) {
  return (
    <View style={styles.statBox}>
      <Text style={styles.statBoxValue}>{(value || 0).toLocaleString()}</Text>
      <Text style={styles.statBoxLabel}>{label}</Text>
    </View>
  );
}

export default function AllianceScreen({ navigation }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const d = await apiGetAlliance();
        setData(d);
      } catch (e) {
        Alert.alert('خطأ', e.message);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  if (loading) return <ActivityIndicator size="large" color="#c0392b" style={{ flex: 1, marginTop: 60 }} />;
  if (!data) return <Text style={styles.empty}>أنت لست في تحالف</Text>;

  const statusLabels = { 0: '✅ حلف', 1: '⏳ مرسل', 2: '📩 وارد' };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Header */}
      <View style={styles.headerCard}>
        <Text style={styles.allianceName}>[{data.name}]</Text>
        {data.full_name ? <Text style={styles.fullName}>{data.full_name}</Text> : null}
        <Text style={styles.rankText}>المرتبة #{data.rank}</Text>
        <Text style={styles.membersText}>{data.members_count}/{data.max_members} أعضاء</Text>
      </View>

      {/* Stats */}
      <View style={styles.statsRow}>
        <StatBox label="هجوم" value={data.points.attack} />
        <StatBox label="دفاع" value={data.points.defense} />
        <StatBox label="هجوم الأسبوع" value={data.points.week_attack} />
        <StatBox label="دفاع الأسبوع" value={data.points.week_defense} />
      </View>

      {/* Description */}
      {data.description?.trim() ? (
        <View style={styles.card}>
          <Text style={styles.cardTitle}>📝 الوصف</Text>
          <Text style={styles.descText}>{data.description}</Text>
        </View>
      ) : null}

      {/* Members */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>👥 الأعضاء ({data.members.length})</Text>
        {data.members.map((m) => (
          <TouchableOpacity
            key={m.id}
            style={styles.memberRow}
            onPress={() => navigation.navigate('WebPage', { path: `profile?uid=${m.id}` })}
          >
            <View style={styles.memberLeft}>
              <OnlineDot hoursAgo={m.hours_ago} />
              <View>
                <Text style={styles.memberName}>{m.name}</Text>
                <Text style={styles.memberRole}>{m.role}</Text>
              </View>
            </View>
            <View style={styles.memberRight}>
              <Text style={styles.memberPop}>{m.population.toLocaleString()}</Text>
              <Text style={styles.memberVillages}>{m.villages} قرى</Text>
            </View>
          </TouchableOpacity>
        ))}
      </View>

      {/* Diplomacy */}
      {(data.contracts.length > 0 || data.wars.length > 0) && (
        <View style={styles.card}>
          <Text style={styles.cardTitle}>🤝 الدبلوماسية</Text>
          {data.contracts.map((c) => (
            <View key={c.alliance_id} style={styles.diploRow}>
              <Text style={styles.diploName}>{c.name}</Text>
              <Text style={styles.diploStatus}>{statusLabels[c.status] || '?'}</Text>
            </View>
          ))}
          {data.wars.map((w) => (
            <View key={w.alliance_id} style={[styles.diploRow, styles.warRow]}>
              <Text style={styles.diploName}>{w.name}</Text>
              <Text style={styles.warStatus}>⚔️ حرب</Text>
            </View>
          ))}
        </View>
      )}
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
  allianceName: { fontSize: 24, fontWeight: '900', color: '#ffd700' },
  fullName: { fontSize: 14, color: '#aaa', marginTop: 4 },
  rankText: { fontSize: 13, color: '#87ceeb', marginTop: 6 },
  membersText: { fontSize: 12, color: '#888', marginTop: 2 },
  statsRow: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    marginBottom: 12, gap: 6,
  },
  statBox: {
    flex: 1, backgroundColor: '#fff', borderRadius: 10, padding: 10,
    alignItems: 'center', borderWidth: 1, borderColor: '#eee',
  },
  statBoxValue: { fontSize: 14, fontWeight: '800', color: '#c0392b' },
  statBoxLabel: { fontSize: 10, color: '#888', marginTop: 2 },
  card: {
    backgroundColor: '#fff', borderRadius: 10, padding: 14,
    marginBottom: 10, borderWidth: 1, borderColor: '#eee',
  },
  cardTitle: {
    fontSize: 15, fontWeight: '800', color: '#333', textAlign: 'right',
    marginBottom: 10, borderBottomWidth: 1, borderBottomColor: '#f0f0f0', paddingBottom: 8,
  },
  descText: { fontSize: 13, color: '#555', textAlign: 'right', lineHeight: 22 },
  memberRow: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    alignItems: 'center', paddingVertical: 10,
    borderBottomWidth: 1, borderBottomColor: '#f8f8f8',
  },
  memberLeft: { flexDirection: 'row-reverse', alignItems: 'center', gap: 8 },
  dot: { width: 8, height: 8, borderRadius: 4 },
  memberName: { fontSize: 14, fontWeight: '700', color: '#333' },
  memberRole: { fontSize: 11, color: '#888' },
  memberRight: { alignItems: 'flex-start' },
  memberPop: { fontSize: 13, fontWeight: '700', color: '#555' },
  memberVillages: { fontSize: 10, color: '#aaa' },
  diploRow: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    paddingVertical: 8, borderBottomWidth: 1, borderBottomColor: '#f8f8f8',
  },
  warRow: { backgroundColor: '#fff5f5' },
  diploName: { fontSize: 13, fontWeight: '600', color: '#333' },
  diploStatus: { fontSize: 12, color: '#27ae60' },
  warStatus: { fontSize: 12, color: '#c0392b', fontWeight: '700' },
});
