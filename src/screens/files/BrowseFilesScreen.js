import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  Alert,
  StyleSheet,
  Linking,
} from 'react-native';
import { fileService } from '../../services/fileService';
import { useAuth } from '../../contexts/AuthContext';
import * as Sharing from 'expo-sharing';
import * as FileSystem from 'expo-file-system';

const BrowseFilesScreen = () => {
  const [files, setFiles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [fileType, setFileType] = useState('');
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const { token } = useAuth();

  useEffect(() => {
    loadFiles();
  }, [page, search, fileType]);

  const loadFiles = async () => {
    try {
      setLoading(true);
      const data = await fileService.getFiles(page, fileType, search);
      setFiles(data.data || []);
      setTotalPages(data.totalPages || 1);
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    setPage(1);
    await loadFiles();
    setRefreshing(false);
  };

  const handleDownload = async (file) => {
    try {
      const downloadUrl = fileService.getDownloadUrl(file._id);
      Alert.alert('Download', `Downloading ${file.title}...`);
      // Note: Actual download requires additional setup
      // For demo, just show the URL
      Alert.alert('Download URL', downloadUrl);
    } catch (err) {
      Alert.alert('Error', 'Failed to download');
    }
  };

  const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  };

  const getFileIcon = (fileType) => {
    switch (fileType) {
      case 'pdf': return '📄';
      case 'doc': return '📝';
      case 'image': return '🖼️';
      default: return '📎';
    }
  };

  const FileTypeFilter = () => (
    <View style={styles.filterContainer}>
      {['', 'pdf', 'doc', 'image'].map((type) => (
        <TouchableOpacity
          key={type || 'all'}
          style={[styles.filterChip, fileType === type && styles.filterChipActive]}
          onPress={() => setFileType(type)}
        >
          <Text style={[styles.filterText, fileType === type && styles.filterTextActive]}>
            {type === '' ? 'All' : type.toUpperCase()}
          </Text>
        </TouchableOpacity>
      ))}
    </View>
  );

  const renderFileItem = ({ item }) => (
    <View style={styles.fileCard}>
      <Text style={styles.fileIcon}>{getFileIcon(item.fileType)}</Text>
      <Text style={styles.fileTitle} numberOfLines={1}>{item.title}</Text>
      <Text style={styles.fileDesc} numberOfLines={2}>{item.description || 'No description'}</Text>
      <View style={styles.fileMeta}>
        <Text style={styles.fileType}>{item.fileType?.toUpperCase()}</Text>
        <Text style={styles.fileSize}>{formatFileSize(item.size)}</Text>
      </View>
      <Text style={styles.fileUser}>Uploaded by {item.uploadedBy?.username}</Text>
      <TouchableOpacity style={styles.downloadButton} onPress={() => handleDownload(item)}>
        <Text style={styles.downloadButtonText}>📥 Download ({item.downloads || 0})</Text>
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
      <TextInput
        style={styles.searchInput}
        placeholder="Search files..."
        value={search}
        onChangeText={setSearch}
        onSubmitEditing={() => { setPage(1); loadFiles(); }}
      />
      <FileTypeFilter />
      <FlatList
        data={files}
        keyExtractor={(item) => item._id}
        renderItem={renderFileItem}
        numColumns={2}
        columnWrapperStyle={styles.row}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={<Text style={styles.emptyText}>No files found</Text>}
      />
      {totalPages > 1 && (
        <View style={styles.pagination}>
          <TouchableOpacity onPress={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1}>
            <Text style={[styles.pageButton, page === 1 && styles.disabled]}>Prev</Text>
          </TouchableOpacity>
          <Text style={styles.pageText}>{page} / {totalPages}</Text>
          <TouchableOpacity onPress={() => setPage(p => Math.min(totalPages, p + 1))} disabled={page === totalPages}>
            <Text style={[styles.pageButton, page === totalPages && styles.disabled]}>Next</Text>
          </TouchableOpacity>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5', padding: 12 },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  searchInput: { backgroundColor: '#fff', borderRadius: 8, padding: 12, fontSize: 16, marginBottom: 12, borderWidth: 1, borderColor: '#e0e0e0' },
  filterContainer: { flexDirection: 'row', marginBottom: 12, gap: 8 },
  filterChip: { paddingHorizontal: 16, paddingVertical: 6, borderRadius: 20, backgroundColor: '#fff', borderWidth: 1, borderColor: '#ddd' },
  filterChipActive: { backgroundColor: '#007AFF', borderColor: '#007AFF' },
  filterText: { fontSize: 12, color: '#666' },
  filterTextActive: { color: '#fff' },
  row: { justifyContent: 'space-between' },
  fileCard: { width: '48%', backgroundColor: '#fff', borderRadius: 12, padding: 12, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4, elevation: 2 },
  fileIcon: { fontSize: 32, textAlign: 'center', marginBottom: 8 },
  fileTitle: { fontSize: 14, fontWeight: '600', color: '#333', textAlign: 'center', marginBottom: 4 },
  fileDesc: { fontSize: 11, color: '#666', textAlign: 'center', marginBottom: 8 },
  fileMeta: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 6 },
  fileType: { fontSize: 10, backgroundColor: '#e3f2fd', paddingHorizontal: 6, paddingVertical: 2, borderRadius: 4, color: '#1976d2' },
  fileSize: { fontSize: 10, color: '#999' },
  fileUser: { fontSize: 10, color: '#999', textAlign: 'center', marginBottom: 8 },
  downloadButton: { backgroundColor: '#007AFF', borderRadius: 6, padding: 8, alignItems: 'center' },
  downloadButtonText: { color: '#fff', fontSize: 12, fontWeight: '500' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  pagination: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', padding: 12, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: '#e0e0e0' },
  pageButton: { paddingHorizontal: 16, paddingVertical: 8, color: '#007AFF', fontWeight: '600' },
  disabled: { color: '#ccc' },
  pageText: { marginHorizontal: 16, fontSize: 14 },
});

export default BrowseFilesScreen;