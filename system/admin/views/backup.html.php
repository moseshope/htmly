<?php
if (login()) {
    if (isset($_GET['file'])) {
        $file = _h($_GET['file']);

        if (!empty($file)) {
            unlink("backup/$file");
        }

    }
}
?>
    <a href="<?php echo site_url() ?>admin/backup-start">Create backup</a>
    <h2>Your backups</h2>
<?php echo get_backup_files() ?>