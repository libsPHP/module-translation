// Translation Types

export interface QueueItem {
  queue_id: number
  entity_type: 'product' | 'category' | 'cms_page' | 'cms_block'
  entity_id: number
  source_language: string
  target_language: string
  status: 'pending' | 'processing' | 'completed' | 'failed' | 'cancelled'
  priority: number
  retry_count: number
  error_message?: string
  created_at: string
  updated_at: string
  processed_at?: string
}

export interface QueueStatus {
  total_items: number
  pending_items: number
  processing_items: number
  completed_items: number
  failed_items: number
  queue_items: QueueItem[]
}

export interface ApiUsage {
  total_requests: number
  successful_requests: number
  failed_requests: number
  total_tokens: number
  total_cost: number
  average_response_time: number
  daily_usage: DailyUsage[]
}

export interface DailyUsage {
  date: string
  requests: number
  tokens: number
  cost: number
}

export interface TranslationHistory {
  history_id: number
  entity_type: string
  entity_id: number
  source_language: string
  target_language: string
  field_name: string
  source_text: string
  translated_text: string
  status: string
  service: string
  tokens_used: number
  cost: number
  created_at: string
}

export interface TranslationResult {
  success: boolean
  entity_id: number
  entity_type: string
  target_language: string
  fields_translated: number
  tokens_used: number
  cost: number
  message: string
}

export interface TranslationConfig {
  api_key: string
  service: 'openai' | 'google' | 'deepl'
  model: string
  auto_translate: boolean
  cache_enabled: boolean
  queue_enabled: boolean
  max_tokens: number
  temperature: number
}

export interface ConnectionTest {
  status: 'success' | 'error'
  message: string
  response_time?: number
  api_version?: string
}

