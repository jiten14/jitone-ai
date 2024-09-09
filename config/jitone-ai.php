<?php

return [
    'default_model' => 'gpt-3.5-turbo-instruct',
    'default_max_tokens' => 150,
    'default_temperature' => 0.7,
    'default_image_size' => '1024x1024',
    'image_model' => 'dall-e-3',  // Specify the image model
    'image_storage_disk' => 'public',
    'image_storage_path' => 'ai-generated-images',
    
    'content_templates' => [
        'product_description' => 'Write a compelling product description for a [product name].',
        'blog_intro' => 'Write an engaging introduction for a blog post about [topic].',
        'email_subject' => 'Create an attention-grabbing email subject line for [purpose].',
        'social_media_post' => 'Craft a social media post promoting [event/product].',
        'seo_meta_description' => 'Write an SEO-friendly meta description for a webpage about [topic].',
        'customer_service_reply' => 'Compose a polite customer service reply addressing [issue].',
        'faq_answer' => 'Provide a clear and concise answer to the FAQ: [question].',
        'press_release_headline' => 'Create a newsworthy headline for a press release about [news item].',
        'video_script_intro' => 'Write an engaging introduction for a video script about [topic].',
        'podcast_episode_summary' => 'Summarize the key points of a podcast episode about [topic].',
    ],
    
    'image_prompts' => [
        'product_showcase' => 'A professional photo of [product] on a white background.',
        'nature_scene' => 'A serene landscape featuring [natural elements].',
        'abstract_concept' => 'An abstract representation of [concept] using vibrant colors.',
        'character_portrait' => 'A detailed portrait of a [character description].',
        'food_photography' => 'A mouth-watering close-up of [dish name].',
        'tech_illustration' => 'A futuristic illustration of [technology].',
        'fashion_design' => 'A stylish outfit featuring [clothing items and style].',
        'book_cover' => 'A captivating book cover design for a [genre] novel titled [book name].',
        'logo_concept' => 'A minimalist logo design for a company named [company name].',
        'infographic_element' => 'A clean and modern infographic element representing [data or concept].',
    ],
];