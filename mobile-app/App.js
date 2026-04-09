import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  SafeAreaView, StatusBar, StyleSheet, View, Text, AppState,
} from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import { getToken, getStoredAuth, apiGetData, clearAuth } from './src/api/client';
import ResourceBar from './src/components/ResourceBar';
import LoginScreen from './src/screens/LoginScreen';
import FieldsScreen from './src/screens/FieldsScreen';
import CityScreen from './src/screens/CityScreen';
import MapScreen from './src/screens/MapScreen';
import MoreScreen from './src/screens/MoreScreen';
import WebPageScreen from './src/screens/WebPageScreen';

const Tab = createBottomTabNavigator();
const Stack = createNativeStackNavigator();

const TAB_ICONS = {
  Fields: '🏕️',
  City: '🏰',
  Map: '🗺️',
  Reports: '📋',
  More: '☰',
};

function TabIcon({ name, focused }) {
  return (
    <Text style={{ fontSize: focused ? 22 : 20, opacity: focused ? 1 : 0.5 }}>
      {TAB_ICONS[name]}
    </Text>
  );
}

function GameTabs({ gameData, onLogout }) {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarIcon: ({ focused }) => <TabIcon name={route.name} focused={focused} />,
        tabBarActiveTintColor: '#ffd700',
        tabBarInactiveTintColor: '#888',
        tabBarStyle: styles.tabBar,
        tabBarLabelStyle: styles.tabLabel,
      })}
      initialRouteName="Fields"
    >
      <Tab.Screen name="Fields" component={FieldsScreen} options={{ tabBarLabel: 'الحقول' }} />
      <Tab.Screen name="City" component={CityScreen} options={{ tabBarLabel: 'المدينة' }} />
      <Tab.Screen name="Map" component={MapScreen} options={{ tabBarLabel: 'الخريطة' }} />
      <Tab.Screen
        name="More"
        options={{ tabBarLabel: 'المزيد' }}
      >
        {(props) => <MoreScreen {...props} onLogout={onLogout} />}
      </Tab.Screen>
    </Tab.Navigator>
  );
}

export default function App() {
  const [authenticated, setAuthenticated] = useState(null); // null = loading
  const [player, setPlayer] = useState(null);
  const [gameData, setGameData] = useState(null);
  const appState = useRef(AppState.currentState);
  const refreshInterval = useRef(null);

  // Check stored auth on start
  useEffect(() => {
    (async () => {
      const token = await getToken();
      if (token) {
        const stored = await getStoredAuth();
        if (stored) {
          setPlayer(stored.player);
          setAuthenticated(true);
          return;
        }
      }
      setAuthenticated(false);
    })();
  }, []);

  // Refresh game data periodically
  const fetchData = useCallback(async () => {
    try {
      const data = await apiGetData();
      setGameData(data);
    } catch (e) {
      // Token expired
      if (e.message === 'Invalid token' || e.message === 'Unauthorized') {
        await clearAuth();
        setAuthenticated(false);
      }
    }
  }, []);

  useEffect(() => {
    if (!authenticated) return;
    fetchData();
    refreshInterval.current = setInterval(fetchData, 30000); // every 30s
    return () => clearInterval(refreshInterval.current);
  }, [authenticated, fetchData]);

  // Pause refresh when app is in background
  useEffect(() => {
    const sub = AppState.addEventListener('change', (next) => {
      if (next === 'active' && authenticated) fetchData();
      appState.current = next;
    });
    return () => sub.remove();
  }, [authenticated, fetchData]);

  const handleLogin = (playerData, villages) => {
    setPlayer(playerData);
    setAuthenticated(true);
  };

  const handleLogout = () => {
    setGameData(null);
    setPlayer(null);
    setAuthenticated(false);
    clearInterval(refreshInterval.current);
  };

  // Loading state
  if (authenticated === null) {
    return (
      <SafeAreaView style={styles.loadingContainer}>
        <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />
        <Text style={styles.loadingTitle}>⚔️ عودة التتار</Text>
        <Text style={styles.loadingSubtitle}>جاري التحميل...</Text>
      </SafeAreaView>
    );
  }

  // Login screen
  if (!authenticated) {
    return (
      <SafeAreaView style={styles.loginContainer}>
        <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />
        <LoginScreen onLogin={handleLogin} />
      </SafeAreaView>
    );
  }

  // Main game
  return (
    <SafeAreaView style={styles.gameContainer}>
      <StatusBar barStyle="light-content" backgroundColor="#3a2a1c" />
      <ResourceBar
        resources={gameData?.resources}
        gold={gameData?.gold}
      />
      <NavigationContainer>
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          <Stack.Screen name="Tabs">
            {(props) => <GameTabs {...props} gameData={gameData} onLogout={handleLogout} />}
          </Stack.Screen>
          <Stack.Screen
            name="WebPage"
            component={WebPageScreen}
            options={{ headerShown: true, headerTitle: '', headerBackTitle: 'رجوع' }}
          />
        </Stack.Navigator>
      </NavigationContainer>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    backgroundColor: '#0d0d1a',
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingTitle: {
    fontSize: 28,
    fontWeight: '900',
    color: '#ffd700',
    marginBottom: 8,
  },
  loadingSubtitle: {
    fontSize: 14,
    color: '#888',
  },
  loginContainer: {
    flex: 1,
    backgroundColor: '#0d0d1a',
  },
  gameContainer: {
    flex: 1,
    backgroundColor: '#3a2a1c',
  },
  tabBar: {
    backgroundColor: '#1b1b2f',
    borderTopWidth: 1,
    borderTopColor: 'rgba(255,255,255,0.06)',
    height: 56,
    paddingBottom: 4,
  },
  tabLabel: {
    fontSize: 10,
    fontWeight: '700',
  },
});
