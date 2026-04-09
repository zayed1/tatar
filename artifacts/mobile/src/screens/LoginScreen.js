import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, KeyboardAvoidingView, Platform,
  ActivityIndicator, Alert,
} from 'react-native';
import { apiLogin, saveAuth } from '../api/client';

export default function LoginScreen({ onLogin }) {
  const [name, setName] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    if (!name.trim() || !password.trim()) {
      Alert.alert('خطأ', 'ادخل اسم المستخدم وكلمة المرور');
      return;
    }
    setLoading(true);
    try {
      const data = await apiLogin(name.trim(), password.trim());
      await saveAuth(data.token, data.player, data.villages);
      onLogin(data.player, data.villages);
    } catch (e) {
      Alert.alert('خطأ', e.message || 'فشل تسجيل الدخول');
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <View style={styles.card}>
        <Text style={styles.title}>⚔️ عودة التتار</Text>
        <Text style={styles.subtitle}>تسجيل الدخول</Text>

        <TextInput
          style={styles.input}
          placeholder="اسم المستخدم"
          placeholderTextColor="#888"
          value={name}
          onChangeText={setName}
          autoCapitalize="none"
          textAlign="right"
        />
        <TextInput
          style={styles.input}
          placeholder="كلمة المرور"
          placeholderTextColor="#888"
          value={password}
          onChangeText={setPassword}
          secureTextEntry
          textAlign="right"
        />

        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
          onPress={handleLogin}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>دخول</Text>
          )}
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0d0d1a',
    justifyContent: 'center',
    paddingHorizontal: 24,
  },
  card: {
    backgroundColor: '#1a1a2e',
    borderRadius: 16,
    padding: 28,
    borderWidth: 1,
    borderColor: '#333',
  },
  title: {
    fontSize: 28,
    fontWeight: '900',
    color: '#ffd700',
    textAlign: 'center',
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 16,
    color: '#aaa',
    textAlign: 'center',
    marginBottom: 24,
  },
  input: {
    backgroundColor: '#0d0d1a',
    borderWidth: 1,
    borderColor: '#444',
    borderRadius: 10,
    padding: 14,
    color: '#fff',
    fontSize: 16,
    marginBottom: 12,
  },
  button: {
    backgroundColor: '#c0392b',
    borderRadius: 10,
    padding: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  buttonDisabled: {
    opacity: 0.7,
  },
  buttonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: '800',
  },
});
