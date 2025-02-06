<?php
// cleanup/index.php
require_once("../../global.php");
include_once("../header.php");

// Only allow authorized admins (roles 1 and 3)
$role = $_SESSION['arole'] ?? 0;
if (!in_array($role, [1, 3])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}

$message = '';
$errors = [];

/**
 * Convert a DB-stored path (e.g., "upload/productgallery/foo.png" or "foo.png")
 * into a consistent "relative" name (e.g., "foo.png") suitable for matching
 * files that we find in a particular local directory.
 */
function unifyDbPath($dbValue, $directory) {
    // Normalize slashes
    $dbValue = str_replace('\\', '/', $dbValue);

    // Remove leading "upload/" if present (e.g., "upload/product/foo.png" => "product/foo.png")
    $dbValue = preg_replace('#^upload/#i', '', $dbValue);

    // Figure out the local subfolder name based on $directory
    // e.g., if $directory = "../../upload/product", localSubfolder might be "product"
    $normDir = str_replace('\\', '/', $directory);
    $normDir = rtrim($normDir, '/'); // e.g. "../../upload/product"

    $localSubfolder = '';
    if (($pos = strpos($normDir, 'upload/')) !== false) {
        $localSubfolder = substr($normDir, $pos + 7); // skip "upload/"
        // e.g., if normDir="../../upload/product" => localSubfolder="product"
    }

    if ($localSubfolder) {
        // Remove "product/" or "productgallery/" from the start of $dbValue if it exists
        $pattern = '#^' . preg_quote($localSubfolder . '/', '#') . '#i';
        $dbValue = preg_replace($pattern, '', $dbValue);
    }

    // Ensure no leading slash remains
    return ltrim($dbValue, '/');
}

/**
 * Convert a real file path from the local directory to a relative name
 * we can compare to the DB path.
 *
 * E.g., if $file = "../../upload/productgallery/foo.png" and $directory
 * is "../../upload/productgallery/", we return "foo.png".
 */
function unifyLocalFile($file, $directory) {
    // Normalize slashes
    $file = str_replace('\\', '/', $file);
    $directory = str_replace('\\', '/', $directory);
    // Ensure directory has trailing slash
    if (substr($directory, -1) !== '/') {
        $directory .= '/';
    }
    // Remove the directory prefix from the file path
    return str_replace($directory, '', $file);
}

/**
 * Returns an array of "relative" filenames (e.g. "foo.png") that are referenced in the DB.
 */
function getReferencedFiles(PDO $pdo, $table, $field, $directory) {
    $stmt = $pdo->prepare("SELECT DISTINCT $field FROM $table WHERE $field IS NOT NULL AND $field <> ''");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $referenced = [];
    foreach ($rows as $dbVal) {
        $relative = unifyDbPath($dbVal, $directory);
        if (!empty($relative)) {
            $referenced[] = $relative;
        }
    }
    return array_unique($referenced);
}

/**
 * Returns an array of local file *paths* (full) found in $directory (non-recursive).
 * Filters out subdirectories so we only return actual files.
 */
function getFilesInDirectory($directory) {
    // Ensure trailing slash
    if (substr($directory, -1) !== '/') {
        $directory .= '/';
    }
    $items = glob($directory . '*');
    if (!is_array($items)) {
        return [];
    }

    // Filter out directories, keep only files
    $files = [];
    foreach ($items as $item) {
        if (is_file($item)) {
            $files[] = $item;
        }
    }
    return $files;
}

// Define all table/field/directory combos based on your new mapping:
$cleanupItems = [
    [
        'label'     => 'Product Main Images',
        'directory' => '../../upload/product/',   // for "products.image"
        'table'     => 'products',
        'field'     => 'image'
    ],
    [
        'label'     => 'Product Gallery Images',
        'directory' => '../../upload/productgallery/',
        'table'     => 'product_images',
        'field'     => 'image_path'
    ],
    [
        'label'     => 'Product Videos',
        'directory' => '../../upload/videos/',
        'table'     => 'product_videos',
        'field'     => 'video_paths'
    ],
    [
        'label'     => 'User Profile Images',
        'directory' => '../../upload/',
        'table'     => 'users',
        'field'     => 'profile'
    ],
    [
        'label'     => 'Subcategory Images',
        'directory' => '../../upload/',
        'table'     => 'subcategories',
        'field'     => 'subcategory_name'
    ],
    [
        'label'     => 'Category Images',
        'directory' => '../../upload/',
        'table'     => 'categories',
        'field'     => 'category_image'
    ],
    [
        'label'     => 'Box Images (Image)',
        'directory' => '../../upload/',
        'table'     => 'box',
        'field'     => 'image'
    ],
    [
        'label'     => 'Box Images (Image2)',
        'directory' => '../../upload/',
        'table'     => 'box',
        'field'     => 'image2'
    ],
    [
        'label'     => 'Boost Plan Images',
        'directory' => '../../upload/',
        'table'     => 'boost_plans',
        'field'     => 'image'
    ],
    [
        'label'     => 'Banner Images',
        'directory' => '../../upload/',
        'table'     => 'banners',
        'field'     => 'image'
    ]
];

$totalUnusedCount = 0;
$unusedFilesData = [];

// Build the data for each item
foreach ($cleanupItems as $item) {
    $dir = $item['directory'];
    // 1) All "referenced" relative paths from the DB
    $referencedFiles = getReferencedFiles($pdo, $item['table'], $item['field'], $dir);

    // 2) All local files in $dir => unify them to relative names
    $filesInDirFull = getFilesInDirectory($dir); // array of "full" paths
    $filesInDirRelative = [];
    foreach ($filesInDirFull as $f) {
        $filesInDirRelative[] = unifyLocalFile($f, $dir);
    }

    // 3) unused = difference
    $unused = array_diff($filesInDirRelative, $referencedFiles);
    $unusedCount = count($unused);
    $totalUnusedCount += $unusedCount;
    $unusedFilesData[] = [
        'label'       => $item['label'],
        'directory'   => $dir,
        'unusedCount' => $unusedCount,
        'unusedFiles' => $unused
    ];
}

// Process cleanup actions if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "Invalid CSRF token!";
    } else {
        $action = $_POST['action'];
        if ($action === 'cleanup_files') {
            // Loop through each cleanup item and delete only the files that are NOT referenced.
            $deletedCount = 0;
            foreach ($unusedFilesData as $itemData) {
                $dir = $itemData['directory'];
                foreach ($itemData['unusedFiles'] as $relativeFile) {
                    $filePath = $dir . $relativeFile;  // reconstruct the full path
                    if (file_exists($filePath) && is_file($filePath)) {
                        unlink($filePath);
                        $deletedCount++;
                    }
                }
            }
            $message = "Unused files cleanup completed. Deleted {$deletedCount} files.";
            // Reset counts after deletion
            $totalUnusedCount = 0;
            $unusedFilesData = [];
        } elseif ($action === 'cleanup_classifieds') {
            // e.g. "cleanup_classifieds.php"
            include 'cleanup_classifieds.php';
            $message = "Expired classifieds cleanup completed.";
        }
    }
}

// Generate a new CSRF token.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mt-5">
    <div class="card shadow border-danger">
        <div class="card-header bg-danger text-white">
            <h3 class="mb-0">Cleanup Actions (Non-Reversible)</h3>
        </div>
        <div class="card-body">
            <p class="mb-4">
                <strong>Warning:</strong> These actions are non-reversible. Once data is removed, it cannot be retrieved.
            </p>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info" id="alert-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <h5>Total Unused Files Found: <?= $totalUnusedCount ?></h5>

            <?php if ($totalUnusedCount > 0): ?>
                <ul class="list-group mb-4">
                    <?php foreach ($unusedFilesData as $data): ?>
                        <li class="list-group-item">
                            <strong><?= $data['label'] ?>:</strong>
                            <?= $data['unusedCount'] ?> unused file<?= ($data['unusedCount'] !== 1 ? 's' : '') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No unused files found.</p>
            <?php endif; ?>

            <form method="post"
                  onsubmit="return confirm('Are you sure you want to proceed with the cleanup? This action cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="submit" name="action" value="cleanup_files" class="btn btn-danger btn-block">
                            Cleanup Unused Files
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="action" value="cleanup_classifieds" class="btn btn-danger btn-block">
                            Cleanup Expired Classifieds
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once("../footer.php"); ?>
