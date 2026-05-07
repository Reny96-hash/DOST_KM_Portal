import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  FlatList,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
  StyleSheet,
} from 'react-native';
import { articleService } from '../../services/articleService';
import ArticleCard from '../../components/ArticleCard';

const ArticlesScreen = ({ navigation }) => {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  useEffect(() => {
    loadArticles();
  }, [page, search]);

  const loadArticles = async () => {
    try {
      setLoading(true);
      const data = await articleService.getArticles(page, 'createdAt', 'desc', search);
      setArticles(data.data || []);
      setTotalPages(data.totalPages || 1);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    setPage(1);
    await loadArticles();
    setRefreshing(false);
  };

  const handleSearch = () => {
    setPage(1);
    loadArticles();
  };

  if (loading && !refreshing) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.centered}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={loadArticles}>
          <Text style={styles.retryText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <TextInput
          style={styles.searchInput}
          placeholder="Search articles..."
          value={search}
          onChangeText={setSearch}
          onSubmitEditing={handleSearch}
        />
      </View>

      {/* Articles List */}
      <FlatList
        data={articles}
        keyExtractor={(item) => item._id}
        renderItem={({ item }) => (
          <ArticleCard
            article={item}
            onPress={(id) => navigation.navigate('ArticleDetail', { id })}
          />
        )}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        ListEmptyComponent={
          <Text style={styles.emptyText}>No articles found</Text>
        }
      />

      {/* Pagination */}
      {totalPages > 1 && (
        <View style={styles.paginationContainer}>
          <TouchableOpacity
            style={[styles.pageButton, page === 1 && styles.disabledButton]}
            onPress={() => setPage(p => Math.max(1, p - 1))}
            disabled={page === 1}
          >
            <Text>Previous</Text>
          </TouchableOpacity>
          <Text style={styles.pageText}>{page} / {totalPages}</Text>
          <TouchableOpacity
            style={[styles.pageButton, page === totalPages && styles.disabledButton]}
            onPress={() => setPage(p => Math.min(totalPages, p + 1))}
            disabled={page === totalPages}
          >
            <Text>Next</Text>
          </TouchableOpacity>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f5f5f5' },
  centered: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  searchContainer: { padding: 12, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#e0e0e0' },
  searchInput: { backgroundColor: '#f0f0f0', borderRadius: 8, padding: 10, fontSize: 16 },
  errorText: { color: 'red', textAlign: 'center', margin: 20 },
  retryButton: { backgroundColor: '#007AFF', padding: 10, borderRadius: 8, marginTop: 10 },
  retryText: { color: '#fff', fontWeight: '600' },
  emptyText: { textAlign: 'center', marginTop: 50, color: '#666' },
  paginationContainer: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', padding: 12, backgroundColor: '#fff', borderTopWidth: 1, borderTopColor: '#e0e0e0' },
  pageButton: { paddingHorizontal: 16, paddingVertical: 8, backgroundColor: '#007AFF', borderRadius: 6, marginHorizontal: 8 },
  disabledButton: { backgroundColor: '#ccc' },
  pageText: { fontSize: 14, color: '#333' },
});

export default ArticlesScreen;