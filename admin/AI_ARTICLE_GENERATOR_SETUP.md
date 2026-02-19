# AI Article Generator Setup Guide

## Overview

The AI Article Generator is a powerful feature that helps you:
- **Discover trending topics** that will rank well in search engines
- **Generate SEO-optimized articles** automatically using AI
- **Publish articles with one click** to boost your website's search rankings

## Features

✅ **Trending Topic Suggestions** - AI analyzes search trends and suggests topics with high SEO potential  
✅ **Auto Article Generation** - Creates complete, SEO-optimized articles based on your selected topics  
✅ **One-Click Publishing** - Publish or save articles as drafts instantly  
✅ **SEO Optimization** - Automatically generates meta titles, descriptions, keywords, and structured content  

## Setup Instructions

### Step 1: Get an OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in to your account
3. Navigate to [API Keys](https://platform.openai.com/api-keys)
4. Click "Create new secret key"
5. Copy your API key (you'll only see it once!)

### Step 2: Configure the API Key

1. Open `admin/config/ai-config.php`
2. Add your API key:
   ```php
   define('OPENAI_API_KEY', 'sk-your-api-key-here');
   ```
3. Save the file

### Alternative: Environment Variable

You can also set the API key as an environment variable:
```bash
export OPENAI_API_KEY='sk-your-api-key-here'
```

### Step 3: Test the Setup

1. Log into your admin panel
2. Navigate to **🤖 AI Article Generator** in the sidebar
3. Click "Get Trending Topics"
4. If you see topic suggestions, setup is complete!

## Usage Guide

### Getting Trending Topics

1. Click **"Get Trending Topics"** button
2. The AI will analyze current trends and suggest 6 topics
3. Each topic includes:
   - Compelling title
   - Relevant keywords
   - Why it's trending (SEO benefit)

### Generating an Article

1. Click on a topic card to select it
2. Review/edit the target keywords if needed
3. Choose article length:
   - **Short**: 500-800 words
   - **Medium**: 1000-1500 words (recommended)
   - **Long**: 2000+ words
4. Click **"Generate Article"**
5. Wait for the AI to create your article (usually 30-60 seconds)

### Publishing Articles

1. Review the generated article preview
2. Click **"Publish Article"** to publish immediately, or
3. Click **"Save as Draft"** to save for later editing

## Cost Information

- **Model Used**: GPT-4o-mini (cost-effective option)
- **Typical Cost per Article**: $0.01 - $0.05
- **Typical Cost per Topic Suggestions**: $0.005 - $0.01

You can monitor your usage at: https://platform.openai.com/usage

## Models Available

You can change the AI model in `admin/config/ai-config.php`:

- `gpt-4o-mini` - Cheapest, good quality (default)
- `gpt-4` - Best quality, more expensive
- `gpt-4-turbo` - Balanced quality and speed

## Troubleshooting

### "AI is not configured" Error

- Check that `OPENAI_API_KEY` is set in `admin/config/ai-config.php`
- Verify the API key is correct and active
- Check your OpenAI account has available credits

### "Failed to generate article" Error

- Check your internet connection
- Verify OpenAI API is accessible from your server
- Check server error logs for detailed error messages
- Ensure you have credits in your OpenAI account

### Fallback Topics

If the AI service is unavailable, the system will automatically show fallback topics that are still valuable for SEO.

## Best Practices

1. **Review Before Publishing**: Always review AI-generated content before publishing
2. **Customize Keywords**: Adjust keywords to match your specific niche
3. **Add Your Expertise**: Consider adding your own insights to AI-generated articles
4. **Regular Publishing**: Publish articles consistently for better SEO results
5. **Monitor Performance**: Track which topics perform best in search rankings

## Security Notes

⚠️ **Important**: Never commit your API key to version control!

- Keep `admin/config/ai-config.php` in `.gitignore`
- Use environment variables in production
- Regularly rotate your API keys

## Support

For issues or questions:
1. Check server error logs
2. Verify API key configuration
3. Test OpenAI API connectivity
4. Review OpenAI usage dashboard for API errors

---

**Happy Blogging! 🚀**
