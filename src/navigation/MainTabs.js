import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createStackNavigator } from '@react-navigation/stack';
import { Ionicons } from '@expo/vector-icons';
import DashboardScreen from '../screens/DashboardScreen';
import ArticlesScreen from '../screens/articles/ArticlesScreen';
import ArticleDetailScreen from '../screens/articles/ArticleDetailScreen';
import MySubmissionsScreen from '../screens/articles/MySubmissionsScreen';
import SubmitArticleScreen from '../screens/articles/SubmitArticleScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';
import BrowseFilesScreen from '../screens/files/BrowseFilesScreen';
import MyFilesScreen from '../screens/files/MyFilesScreen';
import TopicsScreen from '../screens/topics/TopicsScreen';
import PendingArticlesScreen from '../screens/admin/PendingArticlesScreen';
import PendingFilesScreen from '../screens/admin/PendingFilesScreen';
import ManageUsersScreen from '../screens/admin/ManageUsersScreen';
import { useAuth } from '../contexts/AuthContext';

const Tab = createBottomTabNavigator();
const Stack = createStackNavigator();

const ArticlesStack = () => (
  <Stack.Navigator>
    <Stack.Screen name="ArticlesList" component={ArticlesScreen} options={{ title: 'Browse Articles' }} />
    <Stack.Screen name="ArticleDetail" component={ArticleDetailScreen} options={{ title: 'Article Details' }} />
  </Stack.Navigator>
);

const SubmissionsStack = () => (
  <Stack.Navigator>
    <Stack.Screen name="MySubmissionsList" component={MySubmissionsScreen} options={{ title: 'My Submissions' }} />
    <Stack.Screen name="SubmitArticle" component={SubmitArticleScreen} options={{ title: 'Submit Article' }} />
    <Stack.Screen name="ArticleDetail" component={ArticleDetailScreen} options={{ title: 'Article Details' }} />
  </Stack.Navigator>
);

const FilesStack = () => (
  <Stack.Navigator>
    <Stack.Screen name="BrowseFilesList" component={BrowseFilesScreen} options={{ title: 'Browse Files' }} />
    <Stack.Screen name="MyFiles" component={MyFilesScreen} options={{ title: 'My Files' }} />
  </Stack.Navigator>
);

const TopicsStack = () => (
  <Stack.Navigator>
    <Stack.Screen name="TopicsList" component={TopicsScreen} options={{ title: 'Topics' }} />
  </Stack.Navigator>
);

const ProfileStack = () => (
  <Stack.Navigator>
    <Stack.Screen name="Profile" component={ProfileScreen} options={{ title: 'Profile' }} />
  </Stack.Navigator>
);

const AdminStack = () => {
  const { isAdmin } = useAuth();
  if (!isAdmin()) return null;
  
  return (
    <Stack.Navigator>
      <Stack.Screen name="PendingArticles" component={PendingArticlesScreen} options={{ title: 'Pending Articles' }} />
      <Stack.Screen name="PendingFiles" component={PendingFilesScreen} options={{ title: 'Pending Files' }} />
      <Stack.Screen name="ManageUsers" component={ManageUsersScreen} options={{ title: 'Manage Users' }} />
    </Stack.Navigator>
  );
};

const MainTabs = () => {
  const { isAdmin } = useAuth();
  
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName;
          if (route.name === 'Home') iconName = focused ? 'home' : 'home-outline';
          else if (route.name === 'Articles') iconName = focused ? 'library' : 'library-outline';
          else if (route.name === 'My Stuff') iconName = focused ? 'person' : 'person-outline';
          else if (route.name === 'Files') iconName = focused ? 'folder' : 'folder-outline';
          else if (route.name === 'Topics') iconName = focused ? 'pricetags' : 'pricetags-outline';
          else if (route.name === 'Admin') iconName = focused ? 'shield-checkmark' : 'shield-outline';
          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#007AFF',
        tabBarInactiveTintColor: 'gray',
        headerShown: false,
      })}
    >
      <Tab.Screen name="Home" component={DashboardScreen} />
      <Tab.Screen name="Articles" component={ArticlesStack} />
      <Tab.Screen name="Files" component={FilesStack} />
      <Tab.Screen name="Topics" component={TopicsStack} />
      <Tab.Screen name="My Stuff" component={SubmissionsStack} />
      {isAdmin() && <Tab.Screen name="Admin" component={AdminStack} />}
    </Tab.Navigator>
  );
};

export default MainTabs;