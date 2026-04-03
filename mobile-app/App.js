import React, { useState, useRef, useEffect, useCallback } from 'react';
import {
  SafeAreaView,
  StatusBar,
  StyleSheet,
  View,
  ActivityIndicator,
  Text,
  TouchableOpacity,
  BackHandler,
  Platform,
  AppState,
} from 'react-native';
import { WebView } from 'react-native-webview';
import AsyncStorage from '@react-native-async-storage/async-storage';

const GAME_URL = 'https://tatar-production.up.railway.app';
const AUTH_STORAGE_KEY = '@tatar_auth';

export default function App() {
  const webViewRef = useRef(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const [canGoBack, setCanGoBack] = useState(false);
  const [initialUrl, setInitialUrl] = useState(null);
  const appState = useRef(AppState.currentState);

  // Load saved auth on app start
  useEffect(() => {
    AsyncStorage.getItem(AUTH_STORAGE_KEY).then((saved) => {
      if (saved) {
        const { uname, upwd } = JSON.parse(saved);
        // Build auto-login URL with credentials
        setInitialUrl(
          GAME_URL + '/login.php?platform=app&auto=1&savedName=' +
          encodeURIComponent(uname) + '&savedPwd=' + encodeURIComponent(upwd)
        );
      } else {
        setInitialUrl(GAME_URL + '?platform=app');
      }
    }).catch(() => {
      setInitialUrl(GAME_URL + '?platform=app');
    });
  }, []);

  // Android back button
  useEffect(() => {
    if (Platform.OS === 'android') {
      const backHandler = BackHandler.addEventListener('hardwareBackPress', () => {
        if (canGoBack && webViewRef.current) {
          webViewRef.current.goBack();
          return true;
        }
        return false;
      });
      return () => backHandler.remove();
    }
  }, [canGoBack]);

  // Save cookies to disk when app goes to background (Android)
  useEffect(() => {
    const subscription = AppState.addEventListener('change', (nextAppState) => {
      if (appState.current.match(/active/) && nextAppState.match(/inactive|background/)) {
        // Flush cookies when going to background
        if (Platform.OS === 'android' && webViewRef.current) {
          webViewRef.current.injectJavaScript(`
            if (window.CookieManager) { window.CookieManager.flush(); }
            true;
          `);
        }
      }
      appState.current = nextAppState;
    });
    return () => subscription.remove();
  }, []);

  // JS injected into WebView
  const injectedJS = `
    (function() {
      document.body.classList.add('mobile-app');

      if (!document.querySelector('meta[name="viewport"]')) {
        var meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(meta);
      }

      document.addEventListener('gesturestart', function(e) { e.preventDefault(); });

      document.querySelectorAll('a[target="_blank"]').forEach(function(link) {
        link.removeAttribute('target');
      });

      // Intercept successful login to save credentials
      (function watchLogin() {
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
          form.addEventListener('submit', function() {
            var nameInput = form.querySelector('input[name="name"]');
            var pwdInput = form.querySelector('input[name="password"]');
            if (nameInput && pwdInput && nameInput.value && pwdInput.value) {
              window.ReactNativeWebView.postMessage(JSON.stringify({
                type: 'login',
                uname: nameInput.value,
                upwd: pwdInput.value
              }));
            }
          });
        });
      })();

      // Check if user is logged in (not on login page)
      if (!window.location.pathname.match(/login|logout|signup/)) {
        // Try to read game cookie and send to native
        try {
          var cookies = document.cookie;
          if (cookies) {
            window.ReactNativeWebView.postMessage(JSON.stringify({
              type: 'cookies_available',
              cookies: cookies
            }));
          }
        } catch(e) {}
      }

      // If on logout page, clear saved auth
      if (window.location.pathname.match(/logout/)) {
        window.ReactNativeWebView.postMessage(JSON.stringify({ type: 'logout' }));
      }
    })();
    true;
  `;

  // Handle messages from WebView
  const onMessage = useCallback((event) => {
    try {
      const data = JSON.parse(event.nativeEvent.data);
      if (data.type === 'login' && data.uname && data.upwd) {
        AsyncStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify({
          uname: data.uname,
          upwd: data.upwd
        }));
      } else if (data.type === 'logout') {
        AsyncStorage.removeItem(AUTH_STORAGE_KEY);
      }
    } catch (e) {}
  }, []);

  const renderLoading = () => (
    <View style={styles.loadingContainer}>
      <Text style={styles.loadingTitle}>عودة التتار</Text>
      <Text style={styles.loadingSubtitle}>جاري التحميل...</Text>
      <ActivityIndicator size="large" color="#c0392b" style={styles.spinner} />
    </View>
  );

  if (error) {
    return (
      <SafeAreaView style={styles.container}>
        <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />
        <View style={styles.errorContainer}>
          <Text style={styles.errorIcon}>⚔️</Text>
          <Text style={styles.errorTitle}>لا يوجد اتصال</Text>
          <Text style={styles.errorText}>تأكد من اتصالك بالإنترنت وحاول مرة أخرى</Text>
          <TouchableOpacity
            style={styles.retryButton}
            onPress={() => { setError(false); setLoading(true); }}
          >
            <Text style={styles.retryText}>إعادة المحاولة</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  // Wait for initial URL to be determined
  if (!initialUrl) {
    return (
      <SafeAreaView style={styles.container}>
        <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />
        {renderLoading()}
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />

      <WebView
        ref={webViewRef}
        source={{ uri: initialUrl }}
        style={styles.webview}
        injectedJavaScript={injectedJS}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        startInLoadingState={true}
        renderLoading={renderLoading}
        allowsBackForwardNavigationGestures={true}
        pullToRefreshEnabled={true}
        onNavigationStateChange={(navState) => {
          setCanGoBack(navState.canGoBack);
        }}
        onLoadStart={() => setLoading(true)}
        onLoadEnd={() => setLoading(false)}
        onError={() => setError(true)}
        onHttpError={(syntheticEvent) => {
          const { nativeEvent } = syntheticEvent;
          if (nativeEvent.statusCode >= 500) {
            setError(true);
          }
        }}
        onMessage={onMessage}
        mixedContentMode="compatibility"
        allowsInlineMediaPlayback={true}
        mediaPlaybackRequiresUserAction={false}
        // Cookie persistence
        sharedCookiesEnabled={true}
        thirdPartyCookiesEnabled={true}
        incognito={false}
        cacheEnabled={true}
        // iOS: bounce disabled
        bounces={false}
        // User Agent
        applicationNameForUserAgent="TatarWarApp/1.0"
      />

      {loading && (
        <View style={styles.loadingOverlay}>
          {renderLoading()}
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0d0d1a',
  },
  webview: {
    flex: 1,
    backgroundColor: '#e9e9e9',
  },
  loadingContainer: {
    position: 'absolute',
    top: 0, left: 0, right: 0, bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0d0d1a',
  },
  loadingOverlay: {
    position: 'absolute',
    top: 0, left: 0, right: 0, bottom: 0,
    backgroundColor: '#0d0d1a',
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingTitle: {
    fontSize: 32,
    fontWeight: '900',
    color: '#ffffff',
    marginBottom: 8,
  },
  loadingSubtitle: {
    fontSize: 16,
    color: '#9999aa',
    marginBottom: 24,
  },
  spinner: {
    marginTop: 10,
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0d0d1a',
    padding: 40,
  },
  errorIcon: {
    fontSize: 60,
    marginBottom: 20,
  },
  errorTitle: {
    fontSize: 24,
    fontWeight: '800',
    color: '#ffffff',
    marginBottom: 12,
  },
  errorText: {
    fontSize: 16,
    color: '#9999aa',
    textAlign: 'center',
    marginBottom: 30,
    lineHeight: 24,
  },
  retryButton: {
    backgroundColor: '#c0392b',
    paddingHorizontal: 40,
    paddingVertical: 14,
    borderRadius: 10,
  },
  retryText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '700',
  },
});
