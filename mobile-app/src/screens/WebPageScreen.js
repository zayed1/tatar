import React from 'react';
import { Text, View } from 'react-native';
import GameWebView from '../components/GameWebView';

export default function WebPageScreen({ route }) {
  const path = route?.params?.path;
  if (!path) return <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}><Text>خطأ: صفحة غير موجودة</Text></View>;
  return <GameWebView path={path} />;
}
