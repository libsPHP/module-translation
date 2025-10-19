import axios, { AxiosInstance } from 'axios'
import type {
  QueueStatus,
  ApiUsage,
  TranslationHistory,
  TranslationResult,
  TranslationConfig,
  ConnectionTest,
} from '../types/translation'

class TranslationApiService {
  private api: AxiosInstance

  constructor() {
    this.api = axios.create({
      baseURL: '/rest/V1',
      headers: {
        'Content-Type': 'application/json',
      },
    })

    this.api.interceptors.request.use((config) => {
      const token = localStorage.getItem('admin_token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })
  }

  // Authentication
  async login(username: string, password: string): Promise<string> {
    const response = await this.api.post('/integration/admin/token', {
      username,
      password,
    })
    return response.data
  }

  // Queue Management
  async getQueueStatus(): Promise<QueueStatus> {
    const response = await this.api.get('/nativelang/admin/queue/status')
    return response.data
  }

  async getQueueItemDetails(queueId: number): Promise<any> {
    const response = await this.api.get(`/nativelang/admin/queue/${queueId}`)
    return response.data
  }

  async cancelQueueItem(queueId: number): Promise<boolean> {
    const response = await this.api.post(
      `/nativelang/admin/queue/${queueId}/cancel`
    )
    return response.data
  }

  async retryFailedTranslations(limit?: number): Promise<{
    success: number
    failed: number
  }> {
    const response = await this.api.post('/nativelang/admin/queue/retry-failed', {
      limit,
    })
    return response.data
  }

  // API Usage
  async getApiUsage(startDate: string, endDate: string): Promise<ApiUsage> {
    const response = await this.api.get('/nativelang/admin/api-usage', {
      params: { startDate, endDate },
    })
    return response.data
  }

  // Translation Operations
  async forceTranslateProduct(
    productId: number,
    targetLanguage: string,
    overwrite = false
  ): Promise<TranslationResult> {
    const response = await this.api.post(
      `/nativelang/admin/product/${productId}/translate`,
      { targetLanguage, overwrite }
    )
    return response.data
  }

  async forceTranslateCategory(
    categoryId: number,
    targetLanguage: string,
    overwrite = false
  ): Promise<TranslationResult> {
    const response = await this.api.post(
      `/nativelang/admin/category/${categoryId}/translate`,
      { targetLanguage, overwrite }
    )
    return response.data
  }

  // History
  async getTranslationHistory(
    limit = 50,
    offset = 0,
    entityType?: string
  ): Promise<TranslationHistory[]> {
    const response = await this.api.get('/nativelang/admin/history', {
      params: { limit, offset, entityType },
    })
    return response.data
  }

  // Cache Management
  async clearCache(languageCode?: string): Promise<boolean> {
    const response = await this.api.post('/nativelang/admin/cache/clear', {
      languageCode,
    })
    return response.data
  }

  async purgeOldHistory(daysOld = 90): Promise<number> {
    const response = await this.api.post('/nativelang/admin/history/purge', {
      daysOld,
    })
    return response.data
  }

  // Configuration
  async getConfiguration(): Promise<TranslationConfig> {
    const response = await this.api.get('/nativelang/admin/configuration')
    return response.data
  }

  async updateConfiguration(config: Partial<TranslationConfig>): Promise<boolean> {
    const response = await this.api.post('/nativelang/admin/configuration', config)
    return response.data
  }

  async testConnection(): Promise<ConnectionTest> {
    const response = await this.api.get('/nativelang/admin/test-connection')
    return response.data
  }
}

export const translationApi = new TranslationApiService()

