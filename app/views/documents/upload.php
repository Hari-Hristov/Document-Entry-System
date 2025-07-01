<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8" />
    <title>Качване на документ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/Document-Entry-System/public/assets/css/style.css" rel="stylesheet" />
    <script>
        // Dummy required documents for each category
        const requiredDocs = {
            "1": ["Заявление за стипендия", "Заявление за прекъсване на обучение", "Други документи"],
            "2": ["Заявление за магистратура", "Други документи"],
            "3": ["Заявление за записване за специалност", "Други документи"],
            "4": ["Заявление за поправка", "Други документи"]
            // "5" (Без категория) intentionally omitted
        };

        function updateRequiredDocs() {
            const categorySelect = document.getElementById('category');
            const docTypeContainer = document.getElementById('required-doc-container');
            const docTypeSelect = document.getElementById('required_document');
            const selectedCat = categorySelect.value;

            // Clear previous options
            docTypeSelect.innerHTML = '';

            if (requiredDocs[selectedCat]) {
                requiredDocs[selectedCat].forEach(function (doc) {
                    const opt = document.createElement('option');
                    opt.value = doc;
                    opt.textContent = doc;
                    docTypeSelect.appendChild(opt);
                });
                docTypeContainer.classList.remove('hidden');
                docTypeSelect.required = true;
            } else {
                docTypeContainer.classList.add('hidden');
                docTypeSelect.required = false;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateRequiredDocs();
            document.getElementById('category').addEventListener('change', updateRequiredDocs);
        });
    </script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <section class="container py-5 main-section">
        <h1>Качи документ</h1>

        <?php if (!empty($error)): ?>
            <section class="alert alert-danger"><?= htmlspecialchars($error) ?></section>
        <?php endif; ?>

        <?php if (!empty($success) && !empty($access_code)): ?>
            <section class="alert alert-success">
                <?= htmlspecialchars($message ?? 'Документът е качен успешно!') ?><br>
                <strong>Входящ номер: <?= htmlspecialchars($access_code) ?></strong>
                <button class="btn btn-sm btn-outline-secondary mt-2 copy-btn"
                    data-entry="<?= htmlspecialchars($access_code) ?>">Копирай</button>
            </section>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" action="index.php?controller=document&action=upload">
            <section class="mb-3">
                <label for="category" class="form-label">Избери категория</label>
                <select id="category" name="category_id" class="form-select" required>
                    <option value="" selected disabled>Избери категория</option>
                    <option value="1">Отдел Студенти</option>
                    <option value="2">Учебен отдел – Магистри</option>
                    <option value="3">Кандидат-студенти</option>
                    <option value="4">Сесия</option>
                    <option value="5">Без категория</option>
                </select>
            </section>

            <section class="mb-3 hidden" id="required-doc-container">
                <label for="required_document" class="form-label">Избери вид документ</label>
                <select id="required_document" name="document_type" class="form-select">
                    <!-- Options will be populated dynamically -->
                </select>
            </section>

            <section class="mb-3">
                <label for="document" class="form-label">Файл (.zip или .pdf)</label>
                <input type="file" id="document" name="document" class="form-control" required />
            </section>

            <button type="submit" class="btn btn-primary">Качи</button>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy to clipboard functionality for the entry number
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                navigator.clipboard.writeText(this.getAttribute('data-entry')).then(() => {
                    this.textContent = 'Копирано';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
                    setTimeout(() => {
                        this.textContent = 'Копирай';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            });
        });
    </script>
</body>

</html>