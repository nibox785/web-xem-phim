<?php
$title = 'Sửa phim - Admin';
include APP_PATH . '/Views/shared/admin_header.php';
?>

<div class="admin-container">
    <h1>Sửa phim: <?php echo htmlspecialchars($movie['title'] ?? ''); ?></h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_edit_movie">
        <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">

        <div class="form-group">
            <label for="title">Tên phim *</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="release_year">Năm phát hành *</label>
                <input type="number" id="release_year" name="release_year" min="1888" max="<?php echo date("Y") + 5; ?>" required value="<?php echo $movie['release_year'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="studio_id">Studio</label>
            <select id="studio_id" name="studio_id">
                <option value="0">Không chọn studio</option>
                <!-- Studios sẽ được load từ data nếu cần -->
            </select>
        </div>
        
        <div class="form-group">
            <label>Thể loại</label>
            <div class="checkbox-group">
                <?php if (!empty($genres)): ?>
                    <?php foreach ($genres as $genre): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="genre_<?php echo $genre['id']; ?>" name="genres[]" value="<?php echo $genre['id']; ?>"
                                <?php echo in_array($genre['id'], $selected_genres ?? []) ? 'checked' : ''; ?>>
                            <label for="genre_<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="poster_url">URL hình poster</label>
            <input type="url" id="poster_url" name="poster_url" value="<?php echo htmlspecialchars($movie['poster_url'] ?? ''); ?>">
            <?php if (!empty($movie['poster_url'])): ?>
                <div class="poster-preview">
                    <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="Poster Preview" width="100">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="trailer_url">URL trailer / video phát</label>
            <input type="url" id="trailer_url" name="trailer_url" value="<?php echo htmlspecialchars($movie['trailer_url'] ?? ''); ?>">
        </div>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật phim</button>
            <a href="<?php echo htmlspecialchars($base_url ?? '/'); ?>index.php?action=admin_movies" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<?php include APP_PATH . '/Views/shared/admin_footer.php'; ?>
