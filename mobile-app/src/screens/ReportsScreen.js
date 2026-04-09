import React, { useState, useEffect, useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, Alert, ScrollView,
} from 'react-native';
import { apiGetReports, apiReadReport, apiDeleteReport } from '../api/client';

const CATEGORIES = [
  { key: 0, label: 'الكل', icon: '📋' },
  { key: 3, label: 'هجوم', icon: '⚔️' },
  { key: 4, label: 'دفاع', icon: '🛡️' },
  { key: 1, label: 'تجسس', icon: '🔍' },
  { key: 2, label: 'تجارة', icon: '🤝' },
  { key: 5, label: 'تعزيز', icon: '🏃' },
];

const CAT_ICONS = { 1: '🔍', 2: '🤝', 3: '⚔️', 4: '🛡️', 5: '🏃' };

function getResultColor(result, isAttacker) {
  if (result === 0) return '#888';
  if (isAttacker) return result < 50 ? '#27ae60' : '#c0392b';
  return result >= 50 ? '#27ae60' : '#c0392b';
}

function getResultText(result, isAttacker, cat) {
  if (cat === 2) return 'تجارة';
  if (cat === 5) return 'تعزيز';
  if (cat === 1) return 'تجسس';
  if (result === 0) return 'بدون خسائر';
  if (result <= 25) return isAttacker ? 'نصر ساحق' : 'هزيمة ساحقة';
  if (result <= 50) return isAttacker ? 'نصر' : 'هزيمة';
  if (result <= 75) return isAttacker ? 'هزيمة' : 'نصر';
  return isAttacker ? 'هزيمة ساحقة' : 'نصر ساحق';
}

export default function ReportsScreen() {
  const [cat, setCat] = useState(0);
  const [reports, setReports] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedReport, setSelectedReport] = useState(null);
  const [page, setPage] = useState(0);
  const [total, setTotal] = useState(0);

  const fetchReports = useCallback(async (p = 0, refresh = false) => {
    if (!refresh) setLoading(true);
    try {
      const data = await apiGetReports(cat, p);
      setReports(p === 0 ? data.reports : [...reports, ...data.reports]);
      setTotal(data.total);
      setPage(p);
    } catch (e) {
      Alert.alert('خطأ', e.message);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [cat]);

  useEffect(() => {
    setSelectedReport(null);
    setReports([]);
    fetchReports(0);
  }, [cat, fetchReports]);

  const handleRefresh = () => {
    setRefreshing(true);
    fetchReports(0, true);
  };

  const handleOpenReport = async (rpt) => {
    try {
      const full = await apiReadReport(rpt.id);
      setSelectedReport(full);
    } catch (e) {
      Alert.alert('خطأ', e.message);
    }
  };

  const handleDelete = (id) => {
    Alert.alert('حذف', 'هل تريد حذف هذا التقرير؟', [
      { text: 'إلغاء', style: 'cancel' },
      {
        text: 'حذف', style: 'destructive', onPress: async () => {
          try {
            await apiDeleteReport(id);
            setSelectedReport(null);
            fetchReports(0);
          } catch (e) { Alert.alert('خطأ', e.message); }
        }
      },
    ]);
  };

  // Report detail
  if (selectedReport) {
    const bodyParts = (selectedReport.body || '').split(' ');
    return (
      <View style={styles.container}>
        <View style={styles.detailHeader}>
          <TouchableOpacity onPress={() => setSelectedReport(null)}>
            <Text style={styles.backButton}>→ رجوع</Text>
          </TouchableOpacity>
          <TouchableOpacity onPress={() => handleDelete(selectedReport.id)} style={styles.deleteBtnHeader}>
            <Text style={styles.deleteBtnText}>🗑</Text>
          </TouchableOpacity>
        </View>
        <ScrollView style={styles.detailScroll}>
          <View style={styles.detailCard}>
            <Text style={styles.detailIcon}>{CAT_ICONS[selectedReport.category] || '📋'}</Text>
            <Text style={styles.detailTitle}>
              {selectedReport.from_name} ({selectedReport.from_village})
            </Text>
            <Text style={styles.detailVs}>⚔️</Text>
            <Text style={styles.detailTitle}>
              {selectedReport.to_name} ({selectedReport.to_village})
            </Text>
            <Text style={styles.detailDate}>{selectedReport.date}</Text>
            <View style={styles.divider} />

            {selectedReport.category === 3 || selectedReport.category === 4 ? (
              <View>
                <Text style={styles.sectionLabel}>نتيجة المعركة</Text>
                <Text style={[styles.resultBadge, { color: getResultColor(selectedReport.result, true) }]}>
                  {selectedReport.result <= 50 ? 'المهاجم فاز' : 'المدافع فاز'}
                </Text>
                {bodyParts.length >= 4 && (
                  <View style={styles.resourcesRow}>
                    <Text style={styles.resourceItem}>🪵 {bodyParts[0] || 0}</Text>
                    <Text style={styles.resourceItem}>🧱 {bodyParts[1] || 0}</Text>
                    <Text style={styles.resourceItem}>⛏️ {bodyParts[2] || 0}</Text>
                    <Text style={styles.resourceItem}>🌾 {bodyParts[3] || 0}</Text>
                  </View>
                )}
              </View>
            ) : (
              <Text style={styles.bodyText}>{selectedReport.body}</Text>
            )}
          </View>
        </ScrollView>
      </View>
    );
  }

  // Report list
  return (
    <View style={styles.container}>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.catBar} contentContainerStyle={styles.catBarContent}>
        {CATEGORIES.map((c) => (
          <TouchableOpacity key={c.key} style={[styles.catChip, cat === c.key && styles.catChipActive]} onPress={() => setCat(c.key)}>
            <Text style={[styles.catChipText, cat === c.key && styles.catChipTextActive]}>{c.icon} {c.label}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {loading && reports.length === 0 ? (
        <ActivityIndicator size="large" color="#c0392b" style={{ marginTop: 40 }} />
      ) : (
        <FlatList
          data={reports}
          keyExtractor={(item) => String(item.id)}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={handleRefresh} />}
          onEndReached={() => { if (reports.length < total) fetchReports(page + 1); }}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={<Text style={styles.empty}>لا توجد تقارير</Text>}
          renderItem={({ item }) => (
            <TouchableOpacity style={[styles.rptRow, !item.is_read && styles.rptUnread]} onPress={() => handleOpenReport(item)}>
              <View style={styles.rptIcon}>
                <Text style={{ fontSize: 20 }}>{CAT_ICONS[item.category] || '📋'}</Text>
              </View>
              <View style={styles.rptContent}>
                <Text style={[styles.rptTitle, !item.is_read && styles.bold]} numberOfLines={1}>
                  {item.from_name} → {item.to_name}
                </Text>
                <Text style={styles.rptSub} numberOfLines={1}>
                  {item.from_village} → {item.to_village}
                </Text>
              </View>
              <View style={styles.rptRight}>
                <Text style={[styles.rptResult, { color: getResultColor(item.result, item.is_attacker) }]}>
                  {getResultText(item.result, item.is_attacker, item.category)}
                </Text>
                <Text style={styles.rptDate}>{item.date}</Text>
              </View>
            </TouchableOpacity>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  catBar: { backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee', maxHeight: 50 },
  catBarContent: { paddingHorizontal: 8, alignItems: 'center', gap: 6, flexDirection: 'row-reverse' },
  catChip: { paddingHorizontal: 12, paddingVertical: 8, borderRadius: 20, backgroundColor: '#f0f0f0' },
  catChipActive: { backgroundColor: '#c0392b' },
  catChipText: { fontSize: 12, fontWeight: '600', color: '#555' },
  catChipTextActive: { color: '#fff' },
  rptRow: { flexDirection: 'row-reverse', padding: 12, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#f0f0f0', alignItems: 'center' },
  rptUnread: { backgroundColor: '#fef9f0' },
  rptIcon: { width: 36, alignItems: 'center' },
  rptContent: { flex: 1, alignItems: 'flex-end', marginHorizontal: 8 },
  rptTitle: { fontSize: 13, color: '#333', marginBottom: 2 },
  rptSub: { fontSize: 11, color: '#888' },
  rptRight: { alignItems: 'flex-start' },
  rptResult: { fontSize: 11, fontWeight: '700', marginBottom: 2 },
  rptDate: { fontSize: 10, color: '#aaa' },
  bold: { fontWeight: '800' },
  empty: { textAlign: 'center', color: '#999', marginTop: 40, fontSize: 14 },
  // Detail
  detailHeader: { flexDirection: 'row-reverse', justifyContent: 'space-between', alignItems: 'center', padding: 12, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#eee' },
  backButton: { fontSize: 15, color: '#c0392b', fontWeight: '700' },
  deleteBtnHeader: { backgroundColor: '#fee', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6 },
  deleteBtnText: { fontSize: 14 },
  detailScroll: { flex: 1 },
  detailCard: { margin: 12, backgroundColor: '#fff', borderRadius: 10, padding: 16, borderWidth: 1, borderColor: '#eee', alignItems: 'center' },
  detailIcon: { fontSize: 32, marginBottom: 8 },
  detailTitle: { fontSize: 15, fontWeight: '700', color: '#333', textAlign: 'center' },
  detailVs: { fontSize: 18, marginVertical: 4 },
  detailDate: { fontSize: 11, color: '#aaa', marginTop: 4 },
  divider: { height: 1, backgroundColor: '#eee', marginVertical: 12, width: '100%' },
  sectionLabel: { fontSize: 14, fontWeight: '800', color: '#333', textAlign: 'center', marginBottom: 8 },
  resultBadge: { fontSize: 16, fontWeight: '800', textAlign: 'center', marginBottom: 12 },
  resourcesRow: { flexDirection: 'row-reverse', justifyContent: 'center', gap: 16 },
  resourceItem: { fontSize: 13, fontWeight: '600', color: '#555' },
  bodyText: { fontSize: 14, color: '#333', textAlign: 'right', lineHeight: 22 },
});
