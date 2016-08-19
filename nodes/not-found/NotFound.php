<?php

namespace nodes;

use Node;

class NotFound extends Node {
	public function main() { ?>
		<div class="container">
			<h1>Not Found</h1>
			<p>It looks like we don't have what you're looking for.</p>
		</div>
	<?php }
}
