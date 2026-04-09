import React from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet, Alert,
} from 'react-native';
import { clearAuth } from '../api/client';

const MENU_SECTIONS = [
  {
    title: 'اللعبة',
    items: [
      { label: '👤 البروفايل', screen: 'Profile' },
      { label: '📊 الإحصائيات', screen: 'Statistics' },
      { label: '💬 الشات', path: 'chat.php' },
      { label: '🤝 التحالف', path: 'alliance' },
      { label: '⚔️ التحديات', path: 'def.php' },
      { label: '🏪 السوق', path: 'build.php?bid=17' },
    ],
  },
  {
    title: 'الحساب',
    items: [
      { label: '⭐ بلاس', path: 'plus.php?t=2' },
      { label: '💎 شراء ذهب', path: 'payment.php' },
      { label: '🎁 الهدية اليومية', path: 'gift?get' },
    ],
  },
];

export default function MoreScreen({ navigation, onLogout }) {
  const handleLogout = () => {
    Alert.alert('تسجيل الخروج', 'هل أنت متأكد؟', [
      { text: 'إلغاء', style: 'cancel' },
      {
        text: 'خروج',
        style: 'destructive',
        onPress: async () => {
          await clearAuth();
          onLogout();
        },
      },
    ]);
  };

  const openPage = (item) => {
    if (item.screen) {
      navigation.navigate(item.screen);
    } else {
      navigation.navigate('WebPage', { path: item.path, title: '' });
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      {MENU_SECTIONS.map((section) => (
        <View key={section.title} style={styles.section}>
          <Text style={styles.sectionTitle}>{section.title}</Text>
          {section.items.map((item) => (
            <TouchableOpacity
              key={item.path}
              style={styles.menuItem}
              onPress={() => openPage(item)}
            >
              <Text style={styles.menuLabel}>{item.label}</Text>
              <Text style={styles.arrow}>‹</Text>
            </TouchableOpacity>
          ))}
        </View>
      ))}

      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
        <Text style={styles.logoutText}>🚪 تسجيل الخروج</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f2efe6',
  },
  content: {
    padding: 16,
    paddingBottom: 40,
  },
  section: {
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '800',
    color: '#8e1a0e',
    marginBottom: 8,
    textAlign: 'right',
  },
  menuItem: {
    flexDirection: 'row-reverse',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 14,
    borderRadius: 10,
    marginBottom: 6,
    borderWidth: 1,
    borderColor: '#eee',
  },
  menuLabel: {
    fontSize: 15,
    fontWeight: '600',
    color: '#333',
  },
  arrow: {
    fontSize: 18,
    color: '#999',
  },
  logoutButton: {
    backgroundColor: '#c0392b',
    padding: 14,
    borderRadius: 10,
    alignItems: 'center',
    marginTop: 10,
  },
  logoutText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '800',
  },
});
