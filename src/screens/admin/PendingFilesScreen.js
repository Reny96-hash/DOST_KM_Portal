import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Alert,
  Modal,
  TextInput,
  StyleSheet,
} from 'react-native';
import { fileService } from '../../services/fileService';

const PendingFilesScreen = () => {
  const [files, setFiles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedFile, setSelectedFile] = useState(null);
  const [rejectModalVisible, setRejectModalVisible] = useState(false);
  const [rejectReason, setRejectReason] = useState('');

  useEffect(() => {
    loadFiles();
  }, []);

  const loadFiles = async () => {
    try {
      setLoading(true);
      const data = await fileService.getPendingFiles();
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

  const handleApprove = async (file) => {
    Alert.alert(
      'Approve File',
      `Approve "${file.title}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Approve',
          onPress: async () => {
            try {
              await fileService.approveFile(file._id);
              loadFiles();
            } catch (err) {
              Alert.alert('Error', err.message);
            }
          },
        },
      ],
    );
  };

  const handleReject = async () => {
    if (!rejectReason.trim()) {
      Alert.alert('Error', 'Please provide a rejection reason');
      return;
    }

    try {
      await fileService.rejectFile(selectedFile._id, rejectReason);
      setRejectModalVisible(false);
      setRejectReason('');
      loadFiles();
    } catch (err) {
      Alert.alert('Error', err.message);
    }
  };

  const handleDelete = async (file) => {
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

  const renderFileItem = ({ item }) => (
    <View style={styles.fileCard}>
      <Text style={styles.fileTitle}>{item.title}</Text>
      <Text style={styles.fileName}>{item.originalName}</Text>
      <View style={styles.fileMeta}>
        <Text style={styles.fileType}>{item.fileType?.toUpperCase()}</Text>
        <Text style={styles.fileSize}>{formatFileSize(item.size)}</Text>
      </View>
      <Text style={styles.fileUser}>Uploaded by: {item.uploadedBy?.username}</Text>
      <Text style={styles.fileDate}>Date: {new Date(item.createdAt).toLocaleDateString()}</Text>

      <View style={styles.actionButtons}>
        <TouchableOpacity style={styles.approveButton} onPress={() => handleApprove(item)}>
          <Text style={styles.approveButtonText}>✓ Approve</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={styles.rejectButton}
          onPress={() => {
            setSelectedFile(item);
            setRejectModalVisible(true);
          }}
        >
          <Text style={styles.rejectButtonText}>✗ Reject</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.deleteButton} onPress={() => handleDelete(item)}>
          <Text style={styles.deleteButtonText}>🗑️ Delete</Text>
        </TouchableOpacity>
      </View>
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
      <Text style={styles.header}>Pending Files ({files.length})</Text>

      <FlatList
        data={files}
        keyExtractor={(item) => item._id}
        renderItem={renderFileItem}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>No pending files</Text>}
      />

      <Modal visible={rejectModalVisible} animationType="slide" transparent={true}>
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Reject File</Text>
            <Text style={styles.modalSubtitle}>{selectedFile?.title}</Text>

            <TextInput
              style={[styles.modalInput, styles.textArea]}
              placeholder="Rejection reason *"
              value={rejectReason}
              onChangeText={setRejectReason}
              multiline
              numberOfLines={4}
            />

            <View style={styles.modalButtons}>
              <TouchableOpacity style={styles.modalCancel} onPress={() => setRejectModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.modalSubmit} onPress={handleReject}>
                <Text style={styles.modalSubmitText}>Confirm Rejection</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 16, color: '#333' },
  fileCard: { backgroundColor: '#fff', borderRadius: 12, padding: 16, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  fileTitle: { fontSize: 16, fontWeight: '600', color: '#333', marginBottom: 4 },
  fileName: { fontSize: 12, color: '#666', marginBottom: 8 },
  fileMeta: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  fileType: { fontSize: 11, backgroundColor: '#e3f2fd', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 4, color: '#1976d2' },
  fileSize: { fontSize: 11, color: '#999' },
  fileUser: { fontSize: 12, color: '#666', marginBottom: 4 },
  fileDate: { fontSize: 12, color: '#666', marginBottom: 12 },
  actionButtons: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8 },
  approveButton: { backgroundColor: '#2ecc71', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 6 },
  approveButtonText: { color: '#fff', fontWeight: '600' },
  rejectButton: { backgroundColor: '#f1c40f', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 6 },
  rejectButtonText: { color: '#fff', fontWeight: '600' },
  deleteButton: { backgroundColor: '#e74c3c', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 6 },
  deleteButtonText: { color: '#fff', fontWeight: '600' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  modalContainer: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: 'rgba(0,0,0,0.5)' },
  modalContent: { backgroundColor: '#fff', borderRadius: 12, padding: 20, width: '85%', maxWidth: 400 },
  modalTitle: { fontSize: 20, fontWeight: 'bold', marginBottom: 8, textAlign: 'center' },
  modalSubtitle: { fontSize: 14, color: '#666', textAlign: 'center', marginBottom: 16 },
  modalInput: { backgroundColor: '#f8f8f8', borderRadius: 8, padding: 12, fontSize: 16, borderWidth: 1, borderColor: '#e0e0e0', marginBottom: 12 },
  textArea: { minHeight: 100, textAlignVertical: 'top' },
  modalButtons: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 16, gap: 12 },
  modalCancel: { flex: 1, backgroundColor: '#e0e0e0', borderRadius: 8, padding: 12, alignItems: 'center' },
  modalCancelText: { color: '#666', fontSize: 14, fontWeight: '500' },
  modalSubmit: { flex: 1, backgroundColor: '#e74c3c', borderRadius: 8, padding: 12, alignItems: 'center' },
  modalSubmitText: { color: '#fff', fontSize: 14, fontWeight: '500' },
});

export default PendingFilesScreen;