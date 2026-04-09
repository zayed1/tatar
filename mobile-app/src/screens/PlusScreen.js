import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, Alert,
} from 'react-native';
import { apiGetPlusStatus } from '../api/client';

function FeatureCard({ item, gold }) {
  const canAfford = gold >= item.cost;
  return (
    <View style={[styles.featureCard, !canAfford && styles.featureDisabled]}>
      <Text style={styles.featureIcon}>{item.icon}</Text>
      <View style={styles.featureInfo}>
        <Text style={styles.featureName}>{item.name}</Text>
        <Text style={styles.featureDuration}>{item.duration}</Text>
      </View>
      <View style={[styles.costBadge, canAfford ? styles.costAfford : styles.costCantAfford]}>
        <Text style={styles.costText}>💰 {item.cost.toLocaleString()}</Text>
      </View>
    </View>
  );
}

export default function PlusScreen({ navigation }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const d = await apiGetPlusStatus();
        setData(d);
      } catch (e) {
        Alert.alert('خطأ', e.message);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  if (loading) return <ActivityIndicator size="large" color="#c0392b" style={{ flex: 1, marginTop: 60 }} />;
  if (!data) return null;

  const durationFeatures = data.features.filter(f => f.type === 'duration');
  const instantFeatures = data.features.filter(f => f.type === 'instant');

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {/* Gold Balance */}
      <View style={styles.balanceCard}>
        <Text style={styles.balanceTitle}>رصيدك</Text>
        <View style={styles.balanceRow}>
          <View style={styles.balanceItem}>
            <Text style={styles.balanceValue}>💰 {data.gold.toLocaleString()}</Text>
            <Text style={styles.balanceLabel}>ذهب</Text>
          </View>
          <View style={styles.balanceItem}>
            <Text style={styles.balanceValue}>🪙 {data.silver.toLocaleString()}</Text>
            <Text style={styles.balanceLabel}>فضة</Text>
          </View>
        </View>
        <View style={[styles.plusStatus, data.plus_active ? styles.plusActive : styles.plusInactive]}>
          <Text style={styles.plusStatusText}>
            {data.plus_active ? '⭐ بلاس مفعّل' : 'بلاس غير مفعّل'}
          </Text>
        </View>
      </View>

      {/* Buy Gold Button */}
      <TouchableOpacity
        style={styles.buyGoldBtn}
        onPress={() => navigation.navigate('GoldShop')}
      >
        <Text style={styles.buyGoldText}>💎 شراء ذهب</Text>
      </TouchableOpacity>

      {/* Duration Features */}
      <Text style={styles.sectionTitle}>📅 ميزات بمدة</Text>
      {durationFeatures.map((f) => (
        <FeatureCard key={f.id} item={f} gold={data.gold} />
      ))}

      {/* Instant Features */}
      <Text style={styles.sectionTitle}>⚡ ميزات فورية</Text>
      {instantFeatures.map((f) => (
        <FeatureCard key={f.id} item={f} gold={data.gold} />
      ))}

      {/* Active Features */}
      {data.active_features.length > 0 && (
        <>
          <Text style={styles.sectionTitle}>✅ الميزات النشطة</Text>
          {data.active_features.map((af, i) => {
            const hrs = Math.floor(af.remaining_sec / 3600);
            const mins = Math.floor((af.remaining_sec % 3600) / 60);
            return (
              <View key={i} style={styles.activeFeature}>
                <Text style={styles.activeText}>النوع {af.type}</Text>
                <Text style={styles.activeTime}>{hrs}س {mins}د متبقي</Text>
              </View>
            );
          })}
        </>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  content: { padding: 12, paddingBottom: 40 },
  balanceCard: {
    backgroundColor: '#1a1a2e', borderRadius: 14, padding: 20,
    alignItems: 'center', marginBottom: 12,
  },
  balanceTitle: { fontSize: 14, color: '#aaa', marginBottom: 10 },
  balanceRow: { flexDirection: 'row-reverse', gap: 30 },
  balanceItem: { alignItems: 'center' },
  balanceValue: { fontSize: 20, fontWeight: '900', color: '#ffd700' },
  balanceLabel: { fontSize: 11, color: '#888', marginTop: 2 },
  plusStatus: { marginTop: 12, paddingHorizontal: 16, paddingVertical: 6, borderRadius: 20 },
  plusActive: { backgroundColor: '#27ae60' },
  plusInactive: { backgroundColor: '#555' },
  plusStatusText: { color: '#fff', fontSize: 12, fontWeight: '800' },
  buyGoldBtn: {
    backgroundColor: '#c0392b', borderRadius: 10, padding: 14,
    alignItems: 'center', marginBottom: 16,
  },
  buyGoldText: { color: '#fff', fontSize: 16, fontWeight: '800' },
  sectionTitle: {
    fontSize: 15, fontWeight: '800', color: '#333',
    textAlign: 'right', marginBottom: 8, marginTop: 8,
  },
  featureCard: {
    flexDirection: 'row-reverse', alignItems: 'center', backgroundColor: '#fff',
    borderRadius: 10, padding: 12, marginBottom: 6,
    borderWidth: 1, borderColor: '#eee',
  },
  featureDisabled: { opacity: 0.5 },
  featureIcon: { fontSize: 24, marginLeft: 10 },
  featureInfo: { flex: 1, alignItems: 'flex-end' },
  featureName: { fontSize: 14, fontWeight: '700', color: '#333' },
  featureDuration: { fontSize: 11, color: '#888' },
  costBadge: { borderRadius: 12, paddingHorizontal: 10, paddingVertical: 4 },
  costAfford: { backgroundColor: '#e8f5e9' },
  costCantAfford: { backgroundColor: '#fef3f2' },
  costText: { fontSize: 12, fontWeight: '700', color: '#333' },
  activeFeature: {
    flexDirection: 'row-reverse', justifyContent: 'space-between',
    backgroundColor: '#e8f5e9', borderRadius: 8, padding: 10, marginBottom: 4,
  },
  activeText: { fontSize: 13, fontWeight: '600', color: '#2e7d32' },
  activeTime: { fontSize: 12, color: '#555' },
});
