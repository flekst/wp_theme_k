<?php
// ВАЖНО! Кодировка файла должна быть utf8 (без BOM)

// ?fill_db=3000&cats=1|3&tags=tag1|tag2|tag3
// ?fill_db_comments=3000

new Fill_DB; // включаем

class Fill_DB {

	function __construct(){
		add_filter('init', array( & $this, '__fill_db') );
	}

	function __fill_db(){
		wp_suspend_cache_addition( true ); // отключаем кэширование

		// Контент
		// ?fill_db=3000&cats=1|3&tags=tag1|tag2|tag3
		if( isset($_GET['fill_db']) ){
			$limit = (int) $_GET['fill_db'];
			if(!$limit || !is_numeric($limit)) $limit=50;

			$cats = $_GET['cats'] ? $_GET['cats'] : '1';
			$tags = $_GET['tags'] ? $_GET['tags'] : '';

			//запускаем цикл
			for( $i=1; $i<=$limit; $i++ ){
				$rand = (string) rand(1,99999);

				$content = '';
				$gMax = rand(1,30);
				for( $g=0; $g<$gMax; $g++ )
					$content .= "очередная запись под случайным номером: $rand . ";

				$post_date = $this->get_random('2011|2012').'-0'.rand(1,9).'-'.rand(10,30).' 23:25:59';
				/*
				  'ID' =>               //[ <post id> ] Are you updating an existing post?
				  'comment_status' =>      //[ 'closed' | 'open' ]  'closed' means no comments.
				  'ping_status' =>         //[ 'closed' | 'open' ]  'closed' means pingbacks or trackbacks turned off. def:get_option('default_ping_status')
				  'post_author' =>         //[ <user ID> ] The user ID number of the author. def:$user_ID
				  'post_category' =>       //[ array(<category id>, <...>) ] Add some categories.
				  'post_content' =>        //[ <the text of the post> ] The full text of the post.
				  'post_date' =>           //[ Y-m-d H:i:s ] The time post was made.
				  'post_date_gmt' =>       //[ Y-m-d H:i:s ] The time post was made, in GMT.
				  'post_name' =>           //[ <the name> ]  The name (slug) for your post
				  'post_title' =>          //[ <the title> ] The title of your post.
				  'tags_input' =>          //[ '<tag>, <tag>, <...>' ] For tags.
				  'post_content_filtered' => '' // def:''
				  'post_parent' =>         //[ <post ID> ] Sets the parent of the new post. def:
				  'post_excerpt' =>        //[ <an excerpt> ] For all your post excerpt needs. def:
				  'menu_order' =>          //[ <order> ] If new post is a page, sets the order should it appear in the tabs. def:
				  'post_status' =>         //[ 'draft' | 'publish' | 'pending'| 'future' | 'private' ] Set the status of the new post. def:draft
				  'post_type' =>           //[ 'post' | 'page' ] Sometimes you want to post a page. def:post
				  'to_ping' =>             //[ ? ] ? def:
				  'pinged' =>              //[ ? ] ? def:
				  'guid' => ''             // def:''
				  'post_password' => //[ ? ] password for post? def:

				*/
				$postid = wp_insert_post( array(
					'comment_status' => $this->get_random('closed|open'),
					'ping_status'    => $this->get_random('closed|open'),
					'post_category'  => array( $this->get_random($cats) ),
					'post_title'     => "Запись со случайным номером $rand",
					'post_content'   => $content,
					'post_date'      => $post_date,
					'tags_input'     => $this->get_random($tags),
					'post_status'    => 'publish',
				) );

				if( $postid )
					$this->fill_db_with_metadata_add_views( $postid );

				//if($postid) echo $postid.", ";
				//else echo "error";
				flush();
			}
		}

		// Комменты
		// ?fill_db_comments=3000
		if( isset($_GET['fill_db_comments']) ){
			$limit = (int) $_GET['fill_db_comments'];
			if(!$limit || !is_numeric($limit)) $limit=50;

			global $wpdb;
			$IDs = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_type='post' AND comment_status='open' ORDER BY rand() LIMIT 150", ARRAY_A);
			foreach($IDs as $v) $IDsfix[] = $v['ID'];

			//запускаем цикл
			for($i=1; $i<=$limit; $i++){
				$rand = (string) rand(1,99999);

				$content = '';
				for($g=0; $g<5; $g++)
					$content .= "комментарий: $rand . ";
				$post_date = $this->get_random('2009|2011').'-0'.rand(1,9).'-'.rand(10,30).' 23:25:59';

				/*
				$data = array(
					'comment_post_ID' => 1,
					'comment_author' => 'admin',
					'comment_author_email' => 'admin@admin.com',
					'comment_author_url' => 'http://',
					'comment_content' => 'content here',
					'comment_type' => ,
					'comment_parent' => 0,
					'user_id' => 1,
					'comment_author_IP' => '127.0.0.1',
					'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
					'comment_date' => $time,
					'comment_approved' => 1,
				);
				*/
				$postid = wp_insert_comment( array(
					'comment_post_ID' => array_rand($IDsfix),
					'comment_author' => 'commentator name',
					'comment_author_email' => 'admin@admin.com',
					'comment_author_url' => '',
					'comment_content' => $content,
					//'comment_type' =>,
					//'comment_parent' => 0,
					//'user_id' => 1,
					'comment_author_IP' => '127.0.0.1',
					'comment_agent' => 'Opera 10.0',
					'comment_date' => $post_date,
					'comment_approved' => 1,
				) );
				//if($postid) echo $postid.", ";
				//else echo "error";
				flush();
			}

		}
	}

	// Добавляем произвольное поле views к создаваемым постам
	function fill_db_with_metadata_add_views( $post_id ){
		$value = (string) rand(10,9999);
		update_post_meta( $post_id, 'views', $value );
	}

	function get_random( $data = '' ){
		$arg = explode('|', $data);
		$rand_key = array_rand($arg);
		return $arg[$rand_key];
	}

}