<h1 id="networkapi">Network Filesystem API</h1>
    <h2 id="toc">Оглавление</h2>
    <ul>
    <li><a href="#Запросы">Запросы</a></li>
    <li><a href="#Ответы">Ответы</a></li>
    <li><a href="#Методы">Методы</a>
    <ul>
    <li><a href="#token">token</a></li>
    <li><a href="#list">list</a></li>
    <li><a href="#create">create</a></li>
    <li><a href="#read">read</a></li>
    <li><a href="#write">write</a></li>
    <li><a href="#link">link</a></li>
    <li><a href="#unlink">unlink</a></li>
    <li><a href="#rmdir">rmdir</a></li>
    <li><a href="#lookup">lookup</a></li>
    </ul>
    </li>
    </ul>
    <h2 id="Запросы">Запросы</h2>
    <p>Все запросы осуществляются по методу <code>GET</code>. Аргументы передаются в GET-параметрах.</p>
    <p>Путь составляется следующим образом: <code>API_URL</code> / <code>:method</code>. Пример: <code>127.0.0.1:8000/api/read?inode=1234&token=12345678</code>.</p>
    <p>Токен получается в методе <code>token</code>.</p>
    <h2 id="Ответы">Ответы</h2>
    <p>Система поддерживает два типа ответов.</p>
    <ul>
    <li><code>binary</code> — возвращает представление, которое можно скастить в структуру следующего типа на 64-битной системе (по умолчанию):<pre class=" language-c"><code class="prism  language-c"><span class="token keyword">struct</span> result <span class="token punctuation">{</span>
        u64 status<span class="token punctuation">;</span>
        response_type response<span class="token punctuation">;</span>
    <span class="token punctuation">}</span>
    </code></pre>
    </li>
    <li><code>json</code> — JSON-объект:<pre class=" language-json"><code class="prism  language-json"><span class="token punctuation">{</span>
        <span class="token string">"status"</span><span class="token punctuation">:</span> <span class="token number">0</span><span class="token punctuation">,</span>
        <span class="token string">"response"</span><span class="token punctuation">:</span> <span class="token operator">...</span>
    <span class="token punctuation">}</span>
    </code></pre>
    </li>
    </ul>
    <p>Чтобы получить JSON-объект, нужно передать параметр <code>json</code> (с любым значением или без него) или заголовок <code>Accept: application/json</code>. Пример: <code>127.0.0.1:8000/api/read?inode=1234&token=12345678&json=1</code>.</p>
    <p>В случае ненулевого кода ошибки (<code>status</code>) ответ не возвращается.</p>
    <p>Коды ошибок:</p>
    <ul>
    <li>0 — Запрос выполнен успешно.</li>
    <li>1 — Не найден объект по номеру inode.</li>
    <li>2 — Объект не является файлом.</li>
    <li>3 — Объект не является директорией.</li>
    <li>4 — В указанной директории нет записи с таким именем.</li>
    <li>5 — В указанной директории уже есть запись с таким именем.</li>
    <li>6 — Превышен лимит на размер файла (512 байт).</li>
    <li>7 — Превышен лимит на количество записей в директории (16).</li>
    <li>8 — Директория не пуста.</li>
    <li>9 — Превышен лимит на длину названия файла (255 символов).</li>
    <li>> 10 — Другие проблемы с валидацией данных (токена).</li>
    </ul>
    <h2 id="Методы">Методы</h2>
    <h3 id="token"><code>token</code></h3>
    <p>Выдаёт новый токен. В новой файловой системе есть несколько файлов для примера.</p>
    <p><strong>Авторизация не требуется.</strong></p>
    <p>Возможные коды ошибок: 0.</p>
    <h3 id="list"><code>list</code></h3>
    <p>Возвращает информацию о директории.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>inode</code> — номер inode</li>
    </ul>
    <p>Возвращаемое значение:</p>
    <pre class=" language-c"><code class="prism  language-c"><span class="token keyword">struct</span> entries <span class="token punctuation">{</span>
        size_t entries_count<span class="token punctuation">;</span>
        <span class="token keyword">struct</span> entry <span class="token punctuation">{</span>
            <span class="token keyword">unsigned</span> <span class="token keyword">char</span> entry_type<span class="token punctuation">;</span> <span class="token comment">// DT_DIR (4) or DT_REG (8)</span>
            ino_t ino<span class="token punctuation">;</span>
            <span class="token keyword">char</span> name<span class="token punctuation">[</span><span class="token number">256</span><span class="token punctuation">]</span><span class="token punctuation">;</span>
        <span class="token punctuation">}</span> entries<span class="token punctuation">[</span><span class="token number">16</span><span class="token punctuation">]</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>
    </code></pre>
    <p>Возможные коды ошибок: 0, 1, 3.</p>
    <h3 id="create"><code>create</code></h3>
    <p>Создаёт новый объект.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>parent</code> — номер родительской inode</li>
    <li><code>name</code> — название директории</li>
    <li><code>type</code> — тип создаваемого объекта: <code>directory</code> или <code>file</code></li>
    </ul>
    <p>Возвращаемое значение:</p>
    <pre class=" language-c"><code class="prism  language-c">ino_t ino<span class="token punctuation">;</span>
    </code></pre>
    <p>Возможные коды ошибок: 0, 1, 3, 5, 7.</p>
    <h3 id="read"><code>read</code></h3>
    <p>Читает содержимое файла.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>inode</code> — номер inode</li>
    </ul>
    <p>Возвращаемое значение:</p>
    <pre class=" language-c"><code class="prism  language-c"><span class="token keyword">struct</span> content <span class="token punctuation">{</span>
        u64 content_length<span class="token punctuation">;</span>
        <span class="token keyword">char</span> content<span class="token punctuation">[</span>content_length<span class="token punctuation">]</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span><span class="token punctuation">;</span>
    </code></pre>
    <p>Возможные коды ошибок: 0, 1, 2.</p>
    <h3 id="write"><code>write</code></h3>
    <p>Записывает данные в файл.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>inode</code> — номер inode</li>
    <li><code>content</code> — содержимое файла</li>
    </ul>
    <p>Возвращается только код ошибки.</p>
    <p>Возможные коды ошибок: 0, 1, 2, 6.</p>
    <h3 id="link"><code>link</code></h3>
    <p>Создаёт жёсткую ссылку.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>source</code> — номер inode, на которую нужно сослаться</li>
    <li><code>parent</code> — номер родительской директории</li>
    <li><code>name</code> — название ссылки</li>
    </ul>
    <p>Возвращается только код ошибки.</p>
    <p>Возможные коды ошибок: 0, 1, 2, 3, 5, 7.</p>
    <p>Примечания:</p>
    <ul>
    <li>Ошибка 2 возвращается, если <code>source</code> не является файлом, ошибка 3 — если <code>parent</code> не является директорией.</li>
    <li>Ссылки на директории (junction) не реализованы.</li>
    </ul>
    <h3 id="unlink"><code>unlink</code></h3>
    <p>Удаляет файл. Если на данную inode больше никто не ссылается, она также удаляется.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>parent</code> — номер родительской директории</li>
    <li><code>name</code> — название файла</li>
    </ul>
    <p>Возвращается только код ошибки.</p>
    <p>Возможные коды ошибок: 0, 1, 2, 3, 4.</p>
    <h3 id="rmdir"><code>rmdir</code></h3>
    <p>Удаляет пустую директорию.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>parent</code> — номер родительской директории</li>
    <li><code>name</code> — название директории</li>
    </ul>
    <p>Возвращается только код ошибки.</p>
    <p>Возможные коды ошибок: 0, 1, 3, 4, 8.</p>
    <h3 id="lookup"><code>lookup</code></h3>
    <p>Возвращает информацию об объекте.</p>
    <p>Аргументы:</p>
    <ul>
    <li><code>parent</code> — номер родительской директории</li>
    <li><code>name</code> — название объекта.</li>
    </ul>
    <p>Возвращаемое значение:</p>
    <pre class=" language-c"><code class="prism  language-c"><span class="token keyword">struct</span> entry_info <span class="token punctuation">{</span>
        <span class="token keyword">unsigned</span> <span class="token keyword">char</span> entry_type<span class="token punctuation">;</span> <span class="token comment">// DT_DIR (4) or DT_REG (8)</span>
        ino_t ino<span class="token punctuation">;</span>
    <span class="token punctuation">}</span><span class="token punctuation">;</span>
    </code></pre>
    <p>Возможные коды ошибок: 0, 1, 3, 4.</p>
    </div>
