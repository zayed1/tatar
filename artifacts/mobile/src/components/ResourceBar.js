import React from 'react';
import { View, Text, StyleSheet, Image } from 'react-native';

function formatNum(n) {
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
  return String(n);
}

const RES_ICONS = {
  wood: '🪵',
  clay: '🧱',
  iron: '⛏️',
  crop: '🌾',
};

export default function ResourceBar({ resources, gold }) {
  if (!resources) return null;

  return (
    <View style={styles.container}>
      <View style={styles.row}>
        {['wood', 'clay', 'iron', 'crop'].map((type) => {
          const res = resources[type];
          if (!res) return null;
          return (
            <View key={type} style={styles.item}>
              <Text style={styles.icon}>{RES_ICONS[type]}</Text>
              <Text style={styles.value}>{formatNum(res.current)}</Text>
            </View>
          );
        })}
        <View style={styles.goldItem}>
          <Text style={styles.goldIcon}>💰</Text>
          <Text style={styles.goldValue}>{formatNum(gold || 0)}</Text>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#3a2a1c',
    paddingVertical: 6,
    paddingHorizontal: 4,
    borderBottomWidth: 1,
    borderBottomColor: '#6b5234',
  },
  row: {
    flexDirection: 'row-reverse',
    justifyContent: 'space-around',
    alignItems: 'center',
  },
  item: {
    flexDirection: 'row-reverse',
    alignItems: 'center',
    gap: 2,
  },
  icon: {
    fontSize: 12,
  },
  value: {
    color: '#e8dcc8',
    fontSize: 12,
    fontWeight: '700',
    fontVariant: ['tabular-nums'],
  },
  goldItem: {
    flexDirection: 'row-reverse',
    alignItems: 'center',
    backgroundColor: 'rgba(255,215,0,0.15)',
    borderRadius: 10,
    paddingHorizontal: 6,
    paddingVertical: 2,
    gap: 2,
  },
  goldIcon: {
    fontSize: 11,
  },
  goldValue: {
    color: '#ffd700',
    fontSize: 12,
    fontWeight: '800',
  },
});
