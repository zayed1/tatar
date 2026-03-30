import { Tabs } from "expo-router";
import React from "react";

export default function TabLayout() {
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarStyle: { display: "none", height: 0 },
      }}
    >
      <Tabs.Screen
        name="index"
        options={{ title: "عودة التتار", tabBarShowLabel: false }}
      />
    </Tabs>
  );
}
