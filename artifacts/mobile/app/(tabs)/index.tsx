import React, { useRef, useState } from "react";
import {
  StyleSheet,
  View,
  Text,
  ActivityIndicator,
  TouchableOpacity,
  Platform,
} from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";

const GAME_URL = "https://tatar-production.up.railway.app";

// Only import WebView on native platforms
let WebView: any = null;
if (Platform.OS !== "web") {
  WebView = require("react-native-webview").WebView;
}

export default function HomeScreen() {
  const webViewRef = useRef(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const insets = useSafeAreaInsets();

  const injectedJS = `
    (function() {
      document.body.classList.add('mobile-app');
      document.body.style.backgroundColor = '#e9e9e9';
      document.documentElement.style.backgroundColor = '#e9e9e9';

      var meta = document.querySelector('meta[name="viewport"]');
      if (!meta) {
        meta = document.createElement('meta');
        meta.name = 'viewport';
        document.head.appendChild(meta);
      }
      meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';

      // Remove target=_blank from links
      document.querySelectorAll('a[target="_blank"]').forEach(function(l) { l.removeAttribute('target'); });
    })();
    true;
  `;

  if (error) {
    return (
      <View style={[styles.errorContainer, { paddingTop: insets.top }]}>
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
    );
  }

  // On web platform (Replit simulator), use iframe
  if (Platform.OS === "web") {
    return (
      <View style={styles.container}>
        <iframe
          src={GAME_URL + "?platform=app"}
          style={{ width: "100%", height: "100%", border: "none" }}
          title="عودة التتار"
          onLoad={() => setLoading(false)}
        />
      </View>
    );
  }

  // On native (iOS/Android), use WebView
  return (
    <View style={[styles.container, { paddingTop: insets.top, backgroundColor: "#e9e9e9" }]}>
      <WebView
        ref={webViewRef}
        source={{ uri: GAME_URL + "?platform=app" }}
        style={[styles.webview, { backgroundColor: "#e9e9e9" }]}
        injectedJavaScript={injectedJS}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        allowsBackForwardNavigationGestures={true}
        pullToRefreshEnabled={true}
        onLoadStart={() => setLoading(true)}
        onLoadEnd={() => setLoading(false)}
        onError={() => setError(true)}
        onHttpError={(e: any) => { if (e.nativeEvent.statusCode >= 500) setError(true); }}
        mixedContentMode="compatibility"
        sharedCookiesEnabled={true}
        thirdPartyCookiesEnabled={true}
        applicationNameForUserAgent="TatarWarApp/1.0"
        // Persist cookies between sessions
        incognito={false}
        cacheEnabled={true}
        // iOS specific
        allowsInlineMediaPlayback={true}
        bounces={false}
      />
      {loading && (
        <View style={styles.loadingOverlay}>
          <Text style={styles.loadingTitle}>عودة التتار</Text>
          <Text style={styles.loadingSubtitle}>جاري التحميل...</Text>
          <ActivityIndicator size="large" color="#c0392b" />
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#e9e9e9" },
  webview: { flex: 1 },
  loadingOverlay: {
    position: "absolute", top: 0, left: 0, right: 0, bottom: 0,
    justifyContent: "center", alignItems: "center", backgroundColor: "#e9e9e9",
  },
  loadingTitle: { fontSize: 32, fontWeight: "900", color: "#333", marginBottom: 8 },
  loadingSubtitle: { fontSize: 16, color: "#666", marginBottom: 24 },
  errorContainer: {
    flex: 1, justifyContent: "center", alignItems: "center",
    backgroundColor: "#0d0d1a", padding: 40,
  },
  errorIcon: { fontSize: 60, marginBottom: 20 },
  errorTitle: { fontSize: 24, fontWeight: "800", color: "#fff", marginBottom: 12 },
  errorText: { fontSize: 16, color: "#9999aa", textAlign: "center", marginBottom: 30, lineHeight: 24 },
  retryButton: { backgroundColor: "#c0392b", paddingHorizontal: 40, paddingVertical: 14, borderRadius: 10 },
  retryText: { color: "#fff", fontSize: 16, fontWeight: "700" },
});
