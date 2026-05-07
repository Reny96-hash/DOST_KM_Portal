import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Alert,
  StyleSheet,
} from 'react-native';
import * as DocumentPicker from 'expo-document-picker';
import { fileService } from '../../services/fileService';
import { useAuth } from '../../contexts/AuthContext';

const MyFilesScreen = () => {
  const [files, setFiles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [uploading, setUploading] = useState(false);
  const { token } = useAuth();

  useEffect(() => {
    loadFiles();
  }, []);

  const loadFiles = async () => {
    try {
      setLoading(true);
      const data = await fileService.getMyFiles();
      setFiles(data || []);
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await loadFiles();
    setRefreshing(false);
  };

  const handleUpload = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
      });

      if (result.canceled) return;

      const file = result.assets[0];
      
      setUploading(true);
      Alert.alert('Upload', 'Uploading file...');
      
      const response = await fileService.uploadFile(
        file,
        file.name,
        'Uploaded from mobile'
      );
      
      Alert.alert('Success', 'File uploaded successfully! Pending approval.');
      loadFiles();
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setUploading(false);
    }
  };

  const handleDelete = (file) => {
    Alert.alert(
      'Delete File',
      `Are you sure you want to delete "${file.title}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await fileService.deleteFile(file._id);
              loadFiles();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'approved': return '#2ecc71';
      case 'pending': return '#f1c40f';
      case 'rejected': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  const renderFileItem = ({ item }) => (
    <View style={styles.fileCard}>
      <View style={styles.fileHeader}>
        <Text style={styles.fileTitle}>{item.title}</Text>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
          <Text style={styles.statusText}>{item.status}</Text>
        </View>
      </View>
      <Text style={styles.fileName} numberOfLines={1}>{item.originalName}</Text>
      <View style={styles.fileMeta}>
        <Text style={styles.fileType}>{item.fileType?.toUpperCase()}</Text>
        <Text style={styles.fileSize}>{formatFileSize(item.size)}</Text>
      </View>
      <Text style={styles.fileDate}>Uploaded: {new Date(item.createdAt).toLocaleDateString()}</Text>
      {item.status === 'rejected' && item.rejectionReason && (
        <Text style={styles.rejectionText}>Rejected: {item.rejectionReason}</Text>
      )}
      <TouchableOpacity style={styles.deleteButton} onPress={() => handleDelete(item)}>
        <Text style={styles.deleteButtonText}>🗑️ Delete</Text>
      </TouchableOpacity>
    </View>
  );

  if (loading && !refreshing) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <TouchableOpacity style={styles.uploadButton} onPress={handleUpload} disabled={uploading}>
        <Text style={styles.uploadButtonText}>
          {uploading ? 'Uploading...' : '📤 Upload New File'}
        </Text>
      </TouchableOpacity>

      <FlatList
        data={files}
        keyExtractor={(item) => item._id}
        renderItem={renderFileItem}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>No files uploaded yet</Text>}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  uploadButton: { backgroundColor: '#007AFF', borderRadius: 8, padding: 14, alignItems: 'center', marginBottom: 16 },
  uploadButtonText: { color: '#fff', fontSize: 16, fontWeight: '600' },
  fileCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  fileHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
  fileTitle: { fontSize: 16, fontWeight: '600', flex: 1, color: '#333' },
  statusBadge: { paddingHorizontal: 8, paddingVertical: 2, borderRadius: 4 },
  statusText: { color: '#fff', fontSize: 10, fontWeight: '600', textTransform: 'capitalize' },
  fileName: { fontSize: 12, color: '#666', marginBottom: 8 },
  fileMeta: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  fileType: { fontSize: 11, backgroundColor: '#e3f2fd', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 4, color: '#1976d2' },
  fileSize: { fontSize: 11, color: '#999' },
  fileDate: { fontSize: 11, color: '#999', marginBottom: 12 },
  rejectionText: { fontSize: 11, color: '#e74c3c', marginBottom: 8 },
  deleteButton: { backgroundColor: '#e74c3c', borderRadius: 6, padding: 10, alignItems: 'center' },
  deleteButtonText: { color: '#fff', fontSize: 14, fontWeight: '500' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
});

export default MyFilesScreen;