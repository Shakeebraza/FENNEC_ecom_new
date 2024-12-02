<?php
require_once("../../global.php");
include_once('../header.php');

$data = $dbFunctions->getCategories();
?>
<style>
ul {
    padding: 0;
    margin: 0;
    list-style-type: none;
    width: 100%;
}


#parent-list {
    width: 100%;
    margin: 0 auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: white; 
}

.parent-category {
    padding: 20px;
    background-color: white;
    color: black;
    font-size: 18px;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    cursor: move;
    position: relative;
}

.parent-category:hover {
    background-color: #f0f0f0; 
}

.parent-category::after {
 
    font-family: FontAwesome;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}

.child-list {
    margin: 10px 0 0;
    padding-left: 30px;
    background-color: #e5e5e5; 
    border-left: 4px solid black;
}

.child-category {
    padding: 15px;
    background-color: white;
    color: black;
    margin-bottom: 5px;
    font-size: 16px;
    cursor: move;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%; 
}

.child-category:hover {
    background-color: #f1f1f1; 
}


.sortable-placeholder {
    border: 2px dashed #ccc;
    background-color: #fafafa;
    height: 50px;
    margin-bottom: 10px;
}
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                <ul id="parent-list">
                        <?php foreach ($data as $category): ?>
                            <li class="parent-category" data-id="<?php echo $category['id']; ?>">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <img src="<?php echo htmlspecialchars($category['category_image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" />
                                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                                <ul class="child-list">
                                    <?php if (!empty($category['children'])): ?>
                                        <?php foreach ($category['children'] as $subcategory): ?>
                                            <li class="child-category" data-id="<?php echo $subcategory['id']; ?>">
                                                <h4><?php echo htmlspecialchars($subcategory['name']); ?></h4>
                                                <img src="<?php echo htmlspecialchars($subcategory['subcategory_image']); ?>" alt="<?php echo htmlspecialchars($subcategory['name']); ?>" />
                                                <i class="<?php echo htmlspecialchars($subcategory['icon']); ?>"></i>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li>No subcategories available</li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../footer.php');
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
$(function() {
    $("#parent-list").sortable({
        placeholder: "sortable-placeholder",
        handle: ".parent-category",
        items: "> .parent-category",
        update: function(event, ui) {
            const parentOrder = $("#parent-list").sortable("toArray", { attribute: "data-id" });
            $.ajax({
                url: '<?php echo $urlval?>admin/ajax/categories/sortper.php', 
                type: 'POST',
                data: { order: parentOrder },
                success: function(response) {
        
                    console.log("Parent order updated:", response);
                },
                error: function() {
                    alert('An error occurred while updating parent order.');
                }
            });
        }
    });


    $(".child-list").sortable({
        connectWith: ".child-list",
        placeholder: "sortable-placeholder",
        update: function(event, ui) {
            const parentId = $(this).closest(".parent-category").data("id");
            const childOrder = $(this).sortable("toArray", { attribute: "data-id" });

            $.ajax({
                url: '<?php echo $urlval?>admin/ajax/categories/sort.php', 
                type: 'POST',
                data: {
                    parent_id: parentId,
                    order: childOrder
                },
                success: function(response) {
            
                    console.log("Child order updated for parent ID " + parentId + ":", response);
                },
                error: function() {
                    alert('An error occurred while updating child order.');
                }
            });
        }
    }).disableSelection();
});

</script>

</body>
</html>
