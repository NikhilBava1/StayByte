<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?></h1>
            <?php if (isset($page_subtitle)): ?>
                <p class="text-muted"><?php echo htmlspecialchars($page_subtitle); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
