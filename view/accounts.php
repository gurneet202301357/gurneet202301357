<main>
    <h1>My Account</h1>
    <p>Username: <?php echo $username; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <p>Name: <?php echo $userDisplay; ?></p>
    
    <h2>Your Reactions</h2>
    <?php
    // Fetch reactions of the user
    $reactions = getUserReactions($account_id); // Replace with actual function to fetch reactions

    if ($reactions) {
        echo "<ul>";
        foreach ($reactions as $reaction) {
            $post_id = $reaction['post_id'];
            $reaction_type = ucfirst($reaction['reaction']); // Capitalize the first letter

            // Fetch post details by post ID
            $postDetails = getPostDetails($post_id);
            echo "<li>On post: '" . htmlspecialchars($postDetails['message']) . "' - You reacted with: $reaction_type</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>You haven't reacted to any posts yet.</p>";
    }
    ?>
</main>
</body>
</html>
