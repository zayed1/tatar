import React, { useState, useEffect } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, Alert, Linking,
} from 'react-native';
import { apiGetGoldPackages, API_BASE } from '../api/client';

function PackageCard({ pkg, onBuy }) {
  const hasBonus = pkg.gold_plus > pkg.gold;
  const bonus = pkg.gold_plus - pkg.gold;
  return (
    <View style={styles.packageCard}>
      <View style={styles.packageHeader}>
        <Text style={styles.packageName}>{pkg.name}</Text>
        {pkg.plus_days > 0 && (
          <View style={styles.plusBadge}>
            <Text style={styles.plusBadgeText}>+{pkg.plus_days} يوم بلاس</Text>
          </View>
        )}
      </View>
      <View style={styles.packageBody}>
        <View style={styles.goldInfo}>
          <Text style={styles.goldAmount}>💰 {pkg.gold.toLocaleString()}</Text>
          {hasBonus && (
            <Text style={styles.bonusText}>+{bonus.toLocaleString()} مع بلاس</Text>
          )}
        </View>
        <TouchableOpacity style={styles.buyBtn} onPress={() => onBuy(pkg)}>
          <Text style={styles.buyBtnText}>${pkg.cost}</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

export default function GoldShopScreen() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const d = await apiGetGoldPackages();
        setData(d);
      } catch (e) {
        Alert.alert('خطأ', e.message);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const handleBuy = (pkg) => {
    // Open payment page in browser
    const url = `${API_BASE}/payment.php?p=${pkg.index}`;
    Alert.alert(
      'شراء ذهب',
      `${pkg.name}\n💰 ${pkg.gold.toLocaleString()} ذهب\n💵 $${pkg.cost}`,
      [
        { text: 'إلغاء', style: 'cancel' },
        {
          text: 'شراء',
          onPress: () => Linking.openURL(url),
        },
      ]
    );
  };

  if (loading) return <ActivityIndicator size="large" color="#c0392b" style={{ flex: 1, marginTop: 60 }} />;
  if (!data) return null;

  const regular = data.packages.filter(p => !p.name.includes('عرض'));
  const special = data.packages.filter(p => p.name.includes('عرض'));

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <Text style={styles.title}>💎 متجر الذهب</Text>

      {/* Payment Methods */}
      <View style={styles.paymentMethods}>
        <Text style={styles.paymentTitle}>طرق الدفع المتاحة:</Text>
        <View style={styles.paymentRow}>
          {data.payment_methods.map((pm) => (
            <View key={pm.key} style={styles.paymentChip}>
              <Text style={styles.paymentText}>{pm.name}</Text>
            </View>
          ))}
        </View>
      </View>

      {/* Special Offers */}
      {special.length > 0 && (
        <>
          <Text style={styles.sectionTitle}>🔥 عروض خاصة</Text>
          {special.map((pkg) => (
            <PackageCard key={pkg.index} pkg={pkg} onBuy={handleBuy} />
          ))}
        </>
      )}

      {/* Regular Packages */}
      <Text style={styles.sectionTitle}>📦 الباقات</Text>
      {regular.map((pkg) => (
        <PackageCard key={pkg.index} pkg={pkg} onBuy={handleBuy} />
      ))}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f2efe6' },
  content: { padding: 12, paddingBottom: 40 },
  title: { fontSize: 20, fontWeight: '900', color: '#333', textAlign: 'center', marginBottom: 12 },
  paymentMethods: { backgroundColor: '#fff', borderRadius: 10, padding: 12, marginBottom: 16, borderWidth: 1, borderColor: '#eee' },
  paymentTitle: { fontSize: 13, fontWeight: '700', color: '#333', textAlign: 'right', marginBottom: 8 },
  paymentRow: { flexDirection: 'row-reverse', flexWrap: 'wrap', gap: 6 },
  paymentChip: { backgroundColor: '#f0f0f0', borderRadius: 8, paddingHorizontal: 12, paddingVertical: 6 },
  paymentText: { fontSize: 12, fontWeight: '600', color: '#555' },
  sectionTitle: { fontSize: 15, fontWeight: '800', color: '#333', textAlign: 'right', marginBottom: 8, marginTop: 8 },
  packageCard: { backgroundColor: '#fff', borderRadius: 10, padding: 14, marginBottom: 8, borderWidth: 1, borderColor: '#eee' },
  packageHeader: { flexDirection: 'row-reverse', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  packageName: { fontSize: 15, fontWeight: '800', color: '#333' },
  plusBadge: { backgroundColor: '#fff3e0', borderRadius: 8, paddingHorizontal: 8, paddingVertical: 2 },
  plusBadgeText: { fontSize: 10, fontWeight: '700', color: '#e65100' },
  packageBody: { flexDirection: 'row-reverse', justifyContent: 'space-between', alignItems: 'center' },
  goldInfo: { alignItems: 'flex-end' },
  goldAmount: { fontSize: 18, fontWeight: '900', color: '#333' },
  bonusText: { fontSize: 11, color: '#27ae60', fontWeight: '600', marginTop: 2 },
  buyBtn: { backgroundColor: '#c0392b', borderRadius: 10, paddingHorizontal: 20, paddingVertical: 10 },
  buyBtnText: { color: '#fff', fontSize: 16, fontWeight: '800' },
});
