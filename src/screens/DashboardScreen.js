import React from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
} from 'react-native';
import { useAuth } from '../contexts/AuthContext';

const DashboardScreen = ({ navigation }) => {
  const { user, isAdmin } = useAuth();

  const userCards = [
    { title: 'Browse Articles', description: 'Read approved articles', icon: '📖', screen: 'Articles' },
    { title: 'My Submissions', description: 'View your articles', icon: '📝', screen: 'MySubmissions' },
    { title: 'Submit Article', description: 'Share your knowledge', icon: '✍️', screen: 'SubmitArticle' },
    { title: 'Files', description: 'Browse resources', icon: '📁', screen: 'BrowseFiles' },
    { title: 'My Files', description: 'Upload and manage files', icon: '📎', screen: 'MyFiles' },
    { title: 'Topics', description: 'Browse topics', icon: '🏷️', screen: 'Topics' },
    { title: 'Profile', description: 'Update your profile', icon: '👤', screen: 'Profile' },
  ];

  const adminCards = [
    { title: 'Pending Articles', description: 'Review articles', icon: '⏳', screen: 'PendingArticles' },
    { title: 'Pending Files', description: 'Review files', icon: '📎', screen: 'PendingFiles' },
    { title: 'Manage Users', description: 'Manage users', icon: '👥', screen: 'ManageUsers' },
  ];

  const cards = isAdmin() ? [...userCards, ...adminCards] : userCards;

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.welcome}>Welcome, {user?.fullName || user?.username}!</Text>
        <Text style={styles.role}>Role: {user?.role || 'user'}</Text>
        <Text style={styles.email}>Email: {user?.email}</Text>
      </View>

      <View style={styles.grid}>
        {cards.map((card) => (
          <TouchableOpacity
            key={card.title}
            style={styles.card}
            onPress={() => navigation.navigate(card.screen)}
          >
            <Text style={styles.cardIcon}>{card.icon}</Text>
            <Text style={styles.cardTitle}>{card.title}</Text>
            <Text style={styles.cardDesc}>{card.description}</Text>
          </TouchableOpacity>
        ))}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  header: { backgroundColor: '#fff', padding: 20, marginBottom: 12, borderBottomWidth: 1, borderBottomColor: '#e0e0e0' },
  welcome: { fontSize: 22, fontWeight: 'bold', color: '#1a1a1a', marginBottom: 4 },
  role: { fontSize: 14, color: '#666', marginBottom: 2 },
  email: { fontSize: 14, color: '#666' },
  grid: { flexDirection: 'row', flexWrap: 'wrap', padding: 8, justifyContent: 'space-between' },
  card: { width: '48%', backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, alignItems: 'center', shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  cardIcon: { fontSize: 36, marginBottom: 8 },
  cardTitle: { fontSize: 14, fontWeight: '600', color: '#333', textAlign: 'center', marginBottom: 4 },
  cardDesc: { fontSize: 11, color: '#666', textAlign: 'center' },
});

export default DashboardScreen;