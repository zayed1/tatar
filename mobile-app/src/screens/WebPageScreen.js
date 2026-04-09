import React from 'react';
import GameWebView from '../components/GameWebView';

export default function WebPageScreen({ route }) {
  const { path } = route.params;
  return <GameWebView path={path} />;
}
