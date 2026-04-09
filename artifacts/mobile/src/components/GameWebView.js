import React, { useRef, useState, useEffect } from 'react';
import { StyleSheet, View, ActivityIndicator } from 'react-native';
import { WebView } from 'react-native-webview';
import { API_BASE, getToken } from '../api/client';

// CSS injected to strip ALL web chrome and make content fill the screen
const INJECT_JS = `
(function() {
  var style = document.createElement('style');
  style.textContent = \`
    /* Hide ALL web UI elements */
    #dynamic_header, #side_navi, #side_info, #footer, #mfoot,
    .footer-menu, .footer-stopper, .nav-mob, #header, #mtop,
    div#res, div#resWrap, #app-loader, div#n5, a#n1, a#n2, a#n3, a#n4,
    .Plus.Gold, #notif-bell, #notif-panel, #game, .deltimer,
    div#stime, img.time_of_day, img#msfilter,
    .molvp-protection, .molvp-dve, .molvp-protection2 {
      display: none !important;
    }

    /* Reset body */
    html, body {
      margin: 0 !important;
      padding: 0 !important;
      background: #e8dcc8 !important;
      overflow-x: hidden !important;
      -webkit-overflow-scrolling: touch;
    }

    /* Reset wrapper */
    div.wrapper {
      min-width: 0 !important;
      width: 100% !important;
      background: #e8dcc8 !important;
      background-image: none !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    /* Reset layout */
    div#mid {
      float: none !important;
      width: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
      background-image: none !important;
      background: transparent !important;
      display: block !important;
    }

    /* Content fills screen */
    div#content {
      float: none !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #e8dcc8 !important;
      box-shadow: none !important;
      border-radius: 0 !important;
      min-height: 100vh !important;
    }

    /* Village views fill and center */
    div.village1, div.village2, div.map {
      width: 100% !important;
      max-width: 100% !important;
      padding: 0 !important;
      margin: 0 !important;
      background: #e8dcc8 !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
    }

    /* Village map centered and sized */
    div#village_map {
      margin: 0 auto !important;
      float: none !important;
    }

    /* Village title */
    div.village1 h1, div.village2 h1, div.map h1 {
      text-align: center !important;
      width: 100% !important;
      margin: 8px 0 !important;
      font-size: 18px !important;
      position: relative !important;
      right: auto !important;
    }

    /* Map details below map */
    div#map_details {
      float: none !important;
      width: 100% !important;
      padding: 8px !important;
      margin: 0 !important;
    }

    div#map_details table { width: 100% !important; }

    /* Building contract */
    table#building_contract { width: 100% !important; margin-top: 4px !important; }

    /* Tables full width */
    table { max-width: 100% !important; }
  \`;
  document.head.appendChild(style);

  // Disable bounce/zoom
  document.addEventListener('gesturestart', function(e) { e.preventDefault(); });
})();
true;
`;

export default function GameWebView({ path, onNavigate }) {
  const webViewRef = useRef(null);
  const [uri, setUri] = useState(null);

  // Use session endpoint to establish PHP cookies, then redirect to game page
  useEffect(() => {
    (async () => {
      const token = await getToken();
      if (token) {
        setUri(`${API_BASE}/api/session.php?token=${encodeURIComponent(token)}&redirect=${encodeURIComponent(path)}`);
      } else {
        setUri(`${API_BASE}/${path}?platform=app`);
      }
    })();
  }, [path]);

  if (!uri) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#c0392b" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <WebView
        ref={webViewRef}
        source={{ uri }}
        style={styles.webview}
        injectedJavaScript={INJECT_JS}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        startInLoadingState={true}
        renderLoading={() => (
          <View style={styles.loading}>
            <ActivityIndicator size="large" color="#c0392b" />
          </View>
        )}
        applicationNameForUserAgent="TatarWarApp/2.0"
        sharedCookiesEnabled={true}
        bounces={false}
        scrollEnabled={true}
        scalesPageToFit={false}
        showsVerticalScrollIndicator={false}
        showsHorizontalScrollIndicator={false}
        onNavigationStateChange={(navState) => {
          if (onNavigate) onNavigate(navState);
        }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#e8dcc8',
  },
  webview: {
    flex: 1,
    backgroundColor: '#e8dcc8',
  },
  loading: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#e8dcc8',
    justifyContent: 'center',
    alignItems: 'center',
  },
});
