<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Your Ad</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.5;
        }

        /* Navigation */
        .navbar {
            background-color: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 1rem;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: inherit;
        }

        .logo img {
            width: 40px;
            height: 40px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .back-button {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e5e5;
            border-radius: 0.375rem;
            background: white;
            color: #333;
            text-decoration: none;
        }

        /* Main content */
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        /* Promo banner */
        .promo-banner {
            background-color: #ffd1dc;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .promo-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .promo-text {
            color: #7e22ce;
        }

        .check-button {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
        }

        /* Form styles */
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e5e5;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Image upload area */
        .upload-container {
            border: 2px dashed #e5e5e5;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
        }

        .upload-area {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        .upload-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #e5e5e5;
            border-radius: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .upload-icon {
            width: 2rem;
            height: 2rem;
            margin-bottom: 0.5rem;
        }

        /* Boost packages */
        .boost-container {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .boost-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .boost-package {
            border: 1px solid #e5e5e5;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .boost-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .boost-description {
            font-size: 0.875rem;
            color: #666;
        }

        /* Submit button */
        .submit-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 2rem;
        }

        .submit-button:hover {
            background-color: #16a34a;
        }

        /* Radio buttons */
        .radio-group {
            display: flex;
            gap: 1rem;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        /* Character counter */
        .char-counter {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="logo">
                <img src="placeholder.jpg" alt="Fennec Logo">
                <span class="logo-text">Fennec</span>
            </a>
            <a href="/" class="back-button">Back to home</a>
        </div>
    </nav>

    <main class="container">
        <h1 class="page-title">Post an ad for free!</h1>

        <div class="promo-banner">
            <div class="promo-content">
                <img src="placeholder.jpg" alt="Pet" width="80" height="80" style="border-radius: 50%;">
                <div class="promo-text">
                    <p>A new pet is waiting for you...</p>
                    <p style="font-size: 1.125rem; font-weight: 500;">Check out Fennec Pet Section.</p>
                </div>
            </div>
            <button class="check-button">Check out</button>
        </div>

        <form class="form-container">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="country">Location</label>
                    <select class="form-select" id="country" required>
                        <option value="" disabled selected>Select country</option>
                        <option value="us">United States</option>
                        <option value="uk">United Kingdom</option>
                        <option value="ca">Canada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <select class="form-select" id="city" required>
                        <option value="" disabled selected>Select city</option>
                        <option value="la">Los Angeles</option>
                        <option value="ny">New York</option>
                        <option value="ch">Chicago</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="category">Category</label>
                    <select class="form-select" id="category" required>
                        <option value="" disabled selected>Select category</option>
                        <option value="books">Books and DVD</option>
                        <option value="electronics">Electronics</option>
                        <option value="furniture">Furniture</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subcategory">Subcategory</label>
                    <select class="form-select" id="subcategory" required>
                        <option value="" disabled selected>Select subcategory</option>
                        <option value="fiction">Fiction</option>
                        <option value="nonfiction">Non-Fiction</option>
                        <option value="academic">Academic</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input type="text" class="form-input" id="title" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="price">Price</label>
                    <input type="number" class="form-input" id="price" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Condition</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="condition" value="new" required>
                        <span>New</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="condition" value="used">
                        <span>Used</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-textarea" id="description" required></textarea>
                <div class="char-counter">0 / 3000 characters</div>
            </div>

            <div class="form-group">
                <label class="form-label">Add your images</label>
                <div class="upload-container">
                    <div class="upload-area">
                        <label class="upload-placeholder" for="images">
                            <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>Upload</span>
                        </label>
                    </div>
                    <input type="file" id="images" multiple accept="image/*" style="display: none;">
                    <p style="margin-top: 0.5rem; color: #666; font-size: 0.875rem;">3 / 8 images</p>
                </div>
            </div>

            <div class="boost-container">
                <h3 class="boost-title">Boost Your Ad</h3>
                <div class="boost-package">
                    <div class="boost-header">
                        <span>Boost Pack 1</span>
                        <select class="form-select" style="width: auto;">
                            <option value="" disabled selected>Select Option</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="ultimate">Ultimate</option>
                        </select>
                    </div>
                    <p class="boost-description">Short text explaining what Boost Pack 1 is all about. The short text must take 1 to 2 lines.</p>
                </div>

                <div class="boost-package">
                    <div class="boost-header">
                        <span>Boost Pack 2</span>
                        <select class="form-select" style="width: auto;">
                            <option value="" disabled selected>Select Option</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="ultimate">Ultimate</option>
                        </select>
                    </div>
                    <p class="boost-description">Short text explaining what Boost Pack 2 is all about. The short text must take 1 to 2 lines.</p>
                </div>

                <div class="boost-package">
                    <div class="boost-header">
                        <span>Boost Pack 3</span>
                        <select class="form-select" style="width: auto;">
                            <option value="" disabled selected>Select Option</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="ultimate">Ultimate</option>
                        </select>
                    </div>
                    <p class="boost-description">Short text explaining what Boost Pack 3 is all about. The short text must take 1 to 2 lines.</p>
                </div>
            </div>

            <button type="submit" class="submit-button">Post my Ad</button>
        </form>
    </main>
</body>
</html>