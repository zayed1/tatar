import React, { useRef } from 'react';
import { StyleSheet, View, ActivityIndicator } from 'react-native';
import { WebView } from 'react-native-webview';
import { API_BASE } from '../api/client';

// JS injected to hide game chrome and show only content
const INJECT_JS = `
(function() {
  var style = document.createElement('style');
  style.textContent = \`
    #dynamic_header, #side_navi, #side_info, #footer, #mfoot,
    .footer-menu, .footer-stopper, .nav-mob, #header,
    div#res, div#resWrap, #app-loader { display: none !important; }
    body { margin: 0 !important; padding: 0 !important; background: #f2efe6 !important; }
    div.wrapper { min-width: 0 !important; width: 100% !important; background: #f2efe6 !important; }
    div#mid { float: none !important; width: 100% !important; padding: 0 !important; margin: 0 !important; background-image: none !important; }
    div#content { float: none !important; width: 100% !important; max-width: 100% !important; margin-top: 0 !important; padding: 8px !important; }
  \`;
  document.head.appendChild(style);
})();
true;
`;

export default function GameWebView({ path, onNavigate }) {
  const webViewRef = useRef(null);
  const uri = `${API_BASE}/${path}?platform=app`;

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
        pullToRefreshEnabled={true}
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
  },
  webview: {
    flex: 1,
    backgroundColor: '#f2efe6',
  },
  loading: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#f2efe6',
    justifyContent: 'center',
    alignItems: 'center',
  },
});
