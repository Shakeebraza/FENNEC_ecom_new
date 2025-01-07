<style>
.containermain {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    /* Ensure it stays on top */
    top: 0;
    left: 0;
    height: 100vh;
    /* Full height of the viewport */
    width: 100vw;
    /* Full width of the viewport */
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    /* Hidden by default */
    z-index: 999;
}
.containermain2 {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    /* Ensure it stays on top */
    top: 0;
    left: 0;
    height: 100vh;
    /* Full height of the viewport */
    width: 100vw;
    /* Full width of the viewport */
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    /* Hidden by default */
    z-index: 999;
}

.dialog {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 450px;
}

.dialog h2 {
    font-size: 20px;
    margin-bottom: 16px;
    color: #333;
}

.dialog p {
    font-size: 16px;
    color: #666;
    margin-bottom: 16px;
}

.radio-group {
    margin-bottom: 24px;
}

.radio-option {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.radio-option input[type="radio"] {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    accent-color: #1a73e8;
}

.radio-option label {
    font-size: 16px;
    color: #333;
}

.button-group {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.button {
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    border: none;
    font-weight: 500;
}

.button-cancel {
    background-color: white;
    border: 1px solid #ddd;
    color: #333;
}

.button-cancel:hover {
    background-color: #f5f5f5;
}

.button-delete {
    background-color: #28a745;
    color: white;
}

.button-delete:hover {
    background-color: #218838;
}

/* Custom radio button styling */
.radio-option input[type="radio"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border: 2px solid #999;
    border-radius: 50%;
    outline: none;
    position: relative;
}

.radio-option input[type="radio"]:checked {
    border-color: #1a73e8;
}

.radio-option input[type="radio"]:checked::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background-color: #1a73e8;
    border-radius: 50%;
}
</style>
</head>

<div class="containermain">
    <div class="dialog">
        <h2>Are you sure you want to delete this item?</h2>
        <p>If yes, was the item sold?</p>

        <div class="radio-group">
            <div class="radio-option">
                <input type="radio" id="yes" name="sold" value="yes">
                <label for="yes">Yes</label>
            </div>
            <div class="radio-option">
                <input type="radio" id="no" name="sold" value="no">
                <label for="no">No</label>
            </div>
        </div>

        <div class="button-group">
            <button class="button button-cancel" onclick="closePopup()">Cancel</button>
            <button class="button button-delete" onclick="proceedDelete()">Delete</button>
        </div>
    </div>
</div>

<div class="containermain2" id="confirmationModal">
    <div class="dialog">
        <h2>Are you sure you want to delete this item?</h2>
        <p>If yes, was the item sold?</p>

        <div class="radio-group">
            <div class="radio-option">
                <input type="radio" id="yes" name="sold" value="yes">
                <label for="yes">Yes</label>
            </div>
        </div>
        <div class="radio-option">
                <input type="radio" id="no" name="sold" value="no">
                <label for="no">No</label>
            </div>

        <div class="button-group">
            <button class="button button-cancel" onclick="closePopup2()">Cancel</button>
            <button class="button button-delete" onclick="proceedDelete2()">Delete</button>
        </div>
    </div>
</div>

</html>