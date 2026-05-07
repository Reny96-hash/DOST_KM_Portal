import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  StyleSheet,
} from 'react-native';
import { useAuth } from '../../contexts/AuthContext';
import { userService } from '../../services/userService';

const ProfileScreen = () => {
  const { user, logout, token } = useAuth();
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [updating, setUpdating] = useState(false);
  const [formData, setFormData] = useState({ fullName: '', email: '' });

  useEffect(() => {
    loadProfile();
  }, []);

  const loadProfile = async () => {
    try {
      const data = await userService.getProfile();
      setProfile(data);
      setFormData({ fullName: data.fullName || '', email: data.email || '' });
    } catch (err) {
      Alert.alert('Error', 'Failed to load profile');
    } finally {
      setLoading(false);
    }
  };

  const handleUpdate = async () => {
    setUpdating(true);
    try {
      await userService.updateProfile(formData);
      Alert.alert('Success', 'Profile updated');
      loadProfile();
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setUpdating(false);
    }
  };

  const handleLogout = () => {
    Alert.alert('Logout', 'Are you sure you want to logout?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Logout', onPress: () => logout() },
    ]);
  };

  if (loading) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Profile Information</Text>

        <View style={styles.infoRow}>
          <Text style={styles.label}>Username:</Text>
          <Text style={styles.value}>{profile?.username}</Text>
        </View>

        <View style={styles.infoRow}>
          <Text style={styles.label}>Role:</Text>
          <Text style={styles.value}>{profile?.role || 'user'}</Text>
        </View>

        <View style={styles.infoRow}>
          <Text style={styles.label}>Article Count:</Text>
          <Text style={styles.value}>{profile?.articleCount || 0}</Text>
        </View>

        <View style={styles.infoRow}>
          <Text style={styles.label}>Approved Articles:</Text>
          <Text style={styles.value}>{profile?.approvedArticleCount || 0}</Text>
        </View>

        <View style={styles.divider} />

        <Text style={styles.sectionTitle}>Edit Profile</Text>

        <Text style={styles.inputLabel}>Full Name</Text>
        <TextInput
          style={styles.input}
          value={formData.fullName}
          onChangeText={(text) => setFormData({ ...formData, fullName: text })}
          placeholder="Enter full name"
        />

        <Text style={styles.inputLabel}>Email</Text>
        <TextInput
          style={styles.input}
          value={formData.email}
          onChangeText={(text) => setFormData({ ...formData, email: text })}
          placeholder="Enter email"
          keyboardType="email-address"
        />

        <TouchableOpacity style={styles.updateButton} onPress={handleUpdate} disabled={updating}>
          {updating ? <ActivityIndicator color="#fff" /> : <Text style={styles.buttonText}>Update Profile</Text>}
        </TouchableOpacity>

        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <Text style={styles.logoutButtonText}>Logout</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  card: { backgroundColor: '#fff', margin: 16, padding: 20, borderRadius: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 3 },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#333', marginBottom: 16 },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12 },
  label: { fontSize: 14, color: '#666', fontWeight: '500' },
  value: { fontSize: 14, color: '#333' },
  divider: { height: 1, backgroundColor: '#e0e0e0', marginVertical: 20 },
  inputLabel: { fontSize: 14, fontWeight: '500', color: '#333', marginBottom: 6, marginTop: 12 },
  input: { backgroundColor: '#f8f8f8', borderRadius: 8, padding: 12, fontSize: 16, borderWidth: 1, borderColor: '#e0e0e0', marginBottom: 12 },
  updateButton: { backgroundColor: '#007AFF', borderRadius: 8, padding: 14, alignItems: 'center', marginTop: 20 },
  logoutButton: { backgroundColor: '#e74c3c', borderRadius: 8, padding: 14, alignItems: 'center', marginTop: 12 },
  buttonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
  logoutButtonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
});

export default ProfileScreen;