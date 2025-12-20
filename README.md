# Digilopment N8N Client

PHP client library for n8n webhook integration.

## Installation

```bash
composer require digilopment/n8n-client
```

## Configuration

Create a `.env` file in your project root:

```env
N8N_BASE_URL=https://your-n8n-instance.com
N8N_BEARER_TOKEN=your_bearer_token_here
N8N_ENVIRONMENT=production
```

The `.env` file is automatically loaded when you use the Config class.

## Quick Start

### Backend (ajax.php)

```php
<?php

require 'vendor/autoload.php';

use Digilopment\N8NClient\Services\Handler;

$data = [
    'tema' => $_POST['tema'] ?? null,
];

$errorData = [
    'message' => 'Chyba pri generovaní',
    'details' => null,
];

(new Handler())
    ->setData($data)
    ->handleJson('generuj-obsah')
    ->getAll()
    ->ifError($errorData)
    ->getData();
```

### Frontend (index.html)

```html
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Generátor</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; line-height: 1.6; background-color: #f9f9f9; color: #333; }
        h1 { color: #1a1a1a; text-align: center; }
        .input-group { display: flex; gap: 10px; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        input { flex-grow: 1; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-size: 16px; }
        input:focus { border-color: #007bff; }
        button { padding: 12px 24px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px; font-weight: bold; transition: background 0.2s; }
        button:hover { background: #0056b3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        #result { display: none; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-top: 20px; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        #res-title { color: #007bff; margin-top: 0; }
        #res-desc { color: #666; font-size: 1.1em; display: block; margin-bottom: 15px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
        #res-content p { margin-bottom: 15px; }
        .loader { display: none; text-align: center; color: #666; font-style: italic; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🤖 Gemini AI Content Writer</h1>
    
    <div class="input-group">
        <input type="text" id="topic" placeholder="Zadaj tému (napr. Výhody n8n automatizácie)...">
        <button id="btn-generate">Generuj</button>
    </div>
    <div id="loader" class="loader">Gemini premýšľa, vydrž chvíľu...</div>
    <div id="result">
        <h2 id="res-title"></h2>
        <em id="res-desc"></em>
        <hr>
        <div id="res-content"></div>
    </div>
    <script>
        document.getElementById('btn-generate').addEventListener('click', async () => {
            const topic = document.getElementById('topic').value;
            const btn = document.getElementById('btn-generate');
            const loader = document.getElementById('loader');
            const resultDiv = document.getElementById('result');
            if (!topic) return alert('Zadaj tému!');

            btn.disabled = true;
            loader.style.display = 'block';
            resultDiv.style.display = 'none';

            try {
                const formData = new FormData();
                formData.append('tema', topic);

                const response = await fetch('ajax.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) throw new Error('Server neodpovedá (HTTP ' + response.status + ')');
                
                const responseData = await response.json();

                let finalContent = {};

                // Nový formát: {data: {...}, type: 'json'} alebo {data: '...', type: 'text'}
                if (responseData.type === 'json' && responseData.data) {
                    finalContent = responseData.data;
                } else if (responseData.type === 'text' && responseData.data) {
                    finalContent = {
                        title: 'Odpoveď',
                        description: '',
                        content: '<p>' + responseData.data + '</p>'
                    };
                } else {
                    const content = Array.isArray(responseData) ? responseData[0] : responseData;
                    finalContent = typeof content === 'string' ? JSON.parse(content) : content;
                }

                document.getElementById('res-title').innerText = finalContent.title || 'Bez názvu';
                document.getElementById('res-desc').innerText = finalContent.description || '';
                document.getElementById('res-content').innerHTML = finalContent.content || '<p>Žiaden obsah nebol vygenerovaný.</p>';
                
                resultDiv.style.display = 'block';
            } catch (error) {
                console.error('Error details:', error);
                alert('Chyba pri generovaní: ' + error.message);
            } finally {
                btn.disabled = false;
                loader.style.display = 'none';
            }
        });
    </script>
</body>
</html>
```

## License

MIT
