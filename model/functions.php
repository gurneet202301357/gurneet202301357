<?php
    //Connection information for finding and connecting to the MySQL server
    $dsn = 'mysql:host=localhost;dbname=twixitter';
    $dbuser = 'root';
    $dbpass = '';

    $db = new PDO($dsn, $dbuser, $dbpass);
// Add a reaction to a post
function addReaction($post_id, $account_id, $reaction)
{
    global $db;

    // Check if the user has already reacted to this post
    $query = 'SELECT * FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->bindValue(':account_id', $account_id);
    $statement->execute();
    $existing_reaction = $statement->fetch();
    $statement->closeCursor();

    if ($existing_reaction) {
        // If user already reacted, remove the reaction
        $query = 'DELETE FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':post_id', $post_id);
        $statement->bindValue(':account_id', $account_id);
        $statement->execute();
        $statement->closeCursor();
    } else {
        // Add new reaction
        $query = 'INSERT INTO reactions (post_id, account_id, reaction) VALUES (:post_id, :account_id, :reaction)';
        $statement = $db->prepare($query);
        $statement->bindValue(':post_id', $post_id);
        $statement->bindValue(':account_id', $account_id);
        $statement->bindValue(':reaction', $reaction);
        $statement->execute();
        $statement->closeCursor();
    }
}

// Get reactions for a specific post
function getReactions($post_id)
{
    global $db;

    // Get all reactions for a post
    $query = 'SELECT reaction, COUNT(*) AS count FROM reactions WHERE post_id = :post_id GROUP BY reaction';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->execute();
    $reactions = $statement->fetchAll();
    $statement->closeCursor();

    return $reactions;
}

// Get if a user has reacted to a post
function userHasReacted($post_id, $account_id)
{
    global $db;

    // Check if the user has reacted to this post
    $query = 'SELECT * FROM reactions WHERE post_id = :post_id AND account_id = :account_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':post_id', $post_id);
    $statement->bindValue(':account_id', $account_id);
    $statement->execute();
    $reaction = $statement->fetch();
    $statement->closeCursor();

    return $reaction ? $reaction['reaction'] : null; // Return the reaction if exists
}

// Create a list of posts with reactions
function createPostsListWithReactions($posts, $account_id)
{
    if ($posts != NULL) {
        $postsList = "";
        foreach ($posts as $row) {
            $message = $row['message'];
            $id = $row['id'];
            $username = $row['username'];
            $posted_date = $row['posted_date'];
            $post_account_id = $row['account_id'];

            // Get reactions for the post
            $reactions = getReactions($id);
            $reactionButtons = ['love', 'haha', 'sad', 'angry'];
            $userReaction = userHasReacted($id, $account_id); // Get the user's current reaction

            // Start the post HTML
            $postsList .= "<p>$message</p><p><b>Posted by</b> $username on $posted_date</p>";

            // Display reaction buttons
            foreach ($reactionButtons as $reaction) {
                $reactionText = ucfirst($reaction);
                if ($userReaction == $reaction) {
                    $postsList .= "<p>You $reactionText this</p>";
                } else {
                    $postsList .= "<p><a href='twixitter.php?action=reaction&id=$id&reaction=$reaction'>$reactionText</a></p>";
                }
            }

            // Show reactions count for each
            foreach ($reactions as $reaction) {
                $reactionText = ucfirst($reaction['reaction']);
                $postsList .= "<p>$reactionText: {$reaction['count']} reactions</p>";
            }

            // Add edit/delete options if the post belongs to the user
            if ($account_id == $post_account_id)
                $postsList .= "<p><a href='twixitter.php?action=edit&id=$id'>edit</a> | <a href='twixitter.php?action=delete&id=$id'>delete</a></p>";

            $postsList .= "<hr>";
        }
    } else {
        $postsList = "<h2>No one has posted anything!</h2>";
    }
    return $postsList;
}
