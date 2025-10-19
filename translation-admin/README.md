# Translation Admin - Microservice

React + TypeScript admin panel for NativeLang Translation Magento module.

## Features

- 📊 **Dashboard** - Queue status and API usage statistics
- 📝 **Queue Manager** - View and manage translation queue
- 📚 **Translation History** - Complete translation audit log
- ⚙️ **Settings** - Configure API, cache, and queue
- 📈 **Analytics** - Token usage and cost tracking
- 🔄 **Real-time Updates** - Auto-refresh every 30 seconds
- 🧪 **Connection Test** - Test API connectivity

## Tech Stack

- React 18 + TypeScript
- Vite
- Tailwind CSS
- Shadcn/ui components
- React Query (auto-refresh)
- Recharts (analytics)

## Installation

```bash
npm install
```

## Configuration

Edit `vite.config.ts` to set your Magento URL:

```typescript
server: {
  proxy: {
    '/rest': {
      target: 'https://your-magento-site.com',
      changeOrigin: true,
    },
  },
}
```

## Development

```bash
npm run dev
# App at http://localhost:3002
```

## Build

```bash
npm run build
npm run preview
```

## Deployment

### With Magento

```bash
npm run build
cp -r dist/* ../pub/translation-admin/
```

Access at: `https://your-magento-site.com/translation-admin/`

## Features

### Dashboard
- Queue status (pending, processing, completed, failed)
- API usage statistics (requests, tokens, cost)
- Daily usage trends chart
- Real-time updates

### Queue Manager
- View all queue items
- Cancel pending translations
- Retry failed translations
- Bulk retry all failed
- Filter by status

### Translation History
- Complete audit log
- Source and translated text
- Token usage per translation
- Cost per translation
- Filter by entity type

### Settings
- Configure translation service (OpenAI, Google, DeepL)
- Set API key and model
- Toggle auto-translate
- Enable/disable cache
- Enable/disable queue
- Test API connection
- Clear cache
- Purge old history

## API Endpoints

All endpoints require admin authentication.

### Queue
- `GET /rest/V1/nativelang/admin/queue/status`
- `GET /rest/V1/nativelang/admin/queue/:id`
- `POST /rest/V1/nativelang/admin/queue/:id/cancel`
- `POST /rest/V1/nativelang/admin/queue/retry-failed`

### API Usage
- `GET /rest/V1/nativelang/admin/api-usage?startDate=&endDate=`

### Translations
- `POST /rest/V1/nativelang/admin/product/:id/translate`
- `POST /rest/V1/nativelang/admin/category/:id/translate`

### History
- `GET /rest/V1/nativelang/admin/history?limit=&offset=&entityType=`
- `POST /rest/V1/nativelang/admin/history/purge`

### Configuration
- `GET /rest/V1/nativelang/admin/configuration`
- `POST /rest/V1/nativelang/admin/configuration`
- `GET /rest/V1/nativelang/admin/test-connection`

### Cache
- `POST /rest/V1/nativelang/admin/cache/clear`

## Authentication

```typescript
const token = await translationApi.login('admin', 'password')
localStorage.setItem('admin_token', token)
```

## Real-time Updates

Queue status automatically refreshes every 30 seconds to show processing progress.

## License

MIT License

## Support

Open issues on GitLab for support.

