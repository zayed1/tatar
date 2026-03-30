import React, { useState, useRef, useEffect } from 'react';
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
} from 'react-native';
import { WebView } from 'react-native-webview';

// رابط اللعبة على Railway
const GAME_URL = 'https://tatar-production.up.railway.app';

export default function App() {
  const webViewRef = useRef(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const [canGoBack, setCanGoBack] = useState(false);

  // زر الرجوع في Android
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

  // JavaScript يُحقن في الصفحة لتحسين تجربة التطبيق
  const injectedJS = `
    (function() {
      // أضف class للـ body عشان CSS يعرف إنه تطبيق
      document.body.classList.add('mobile-app');

      // أضف meta viewport لو ما موجود
      if (!document.querySelector('meta[name="viewport"]')) {
        var meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(meta);
      }

      // امنع zoom بالأصابع
      document.addEventListener('gesturestart', function(e) { e.preventDefault(); });

      // حسّن الروابط الخارجية
      document.querySelectorAll('a[target="_blank"]').forEach(function(link) {
        link.removeAttribute('target');
      });
    })();
    true;
  `;

  // شاشة التحميل
  const renderLoading = () => (
    <View style={styles.loadingContainer}>
      <Text style={styles.loadingTitle}>عودة التتار</Text>
      <Text style={styles.loadingSubtitle}>جاري التحميل...</Text>
      <ActivityIndicator size="large" color="#c0392b" style={styles.spinner} />
    </View>
  );

  // شاشة الخطأ
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
            onPress={() => {
              setError(false);
              setLoading(true);
            }}
          >
            <Text style={styles.retryText}>إعادة المحاولة</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0d0d1a" />

      <WebView
        ref={webViewRef}
        source={{ uri: GAME_URL + '?platform=app' }}
        style={styles.webview}
        injectedJavaScript={injectedJS}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        startInLoadingState={true}
        renderLoading={renderLoading}
        allowsBackForwardNavigationGestures={true}
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
        // إعدادات الأمان
        mixedContentMode="compatibility"
        allowsInlineMediaPlayback={true}
        mediaPlaybackRequiresUserAction={false}
        // إعدادات الكوكيز
        sharedCookiesEnabled={true}
        thirdPartyCookiesEnabled={true}
        // User Agent مخصص
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
    backgroundColor: '#0d0d1a',
  },
  // شاشة التحميل
  loadingContainer: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0d0d1a',
  },
  loadingOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
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
  // شاشة الخطأ
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
