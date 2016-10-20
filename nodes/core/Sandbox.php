<?php

namespace nodes\core;

use Core;
use core\models\Node;

class Sandbox extends Node {
	public function main() {
		?>
		<header class="pad-row-6 bg-2 text-5 text-center">
			This is the header!
		</header>
		<main class="row flex-page">
			<nav class="col-2-4 pad-row-8 text-center bg-1">
				I am the menu
			</nav>
			<div id="page" class="col-2-20 pad-bottom-8">
				<section>
					<h3>Color Swatches:</h3>
					<div class="row text-center">
						<div class="col-4-4 pad-row-4 bg-1">.bg-1</div>
						<div class="col-4-4 pad-row-4 bg-2">.bg-2</div>
						<div class="col-4-4 pad-row-4 bg-3">.bg-3</div>
						<div class="col-4-4 pad-row-4 bg-4">.bg-4</div>
						<div class="col-4-4 pad-row-4 bg-5">.bg-5</div>
						<div class="col-4-4 pad-row-4 bg-6">.bg-6</div>
					</div>
					<p class="color-1">.color-1</p>
					<p class="color-2">.color-2</p>
					<p class="color-3">.color-3</p>
					<p class="color-4">.color-4</p>
					<p class="color-5">.color-5</p>
					<p class="color-6">.color-6</p>
				</section>
				<section class="pad-col-3">
					<h3>Sizes:</h3>
					<p>default size</p>
					<p class="text-1">.text-1</p>
					<p class="text-2">.text-2</p>
					<p class="text-3">.text-3 (default)</p>
					<p class="text-4">.text-4</p>
					<p class="text-5">.text-5</p>
					<p class="text-6">.text-6</p>
					<p class="text-7">.text-7</p>
					<p class="text-8">.text-8</p>
					<p class="text-9">.text-9</p>
					<p class="text-10">.text-10</p>
					<h1>I am an h1</h1>
					<h2>I am an h2</h2>
					<h3>I am an h3</h3>
					<h4>I am an h4</h4>
					<h5>I am an h5</h5>
					<h6>I am an h6</h6>
					<p>Some paragraphs...</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				</section>
				<section class="pad-col-3">
				<h3>Buttons:</h3>
					<button>default button</button>
					<button class="btn-1">.btn-1</button>
					<a class="btn btn-2">a.btn.btn-2</a>
					<button class="btn-3">.btn-3</button>
					<a class="btn btn-4">a.btn.btn-4</a>
					<button class="btn-5">.btn-5</button>
					<a class="btn btn-6">a.btn.btn-6</a>
					<button class="block push-top-3 pad-row-3">I am a fat button (.block)</button>
				</section>
				<section class="pad-col-3">
					<h3>Form Controls:</h3>
					<input>
					<input type="date">
					<input type="email">
					<input type="number">
					<input type="tel">
					<input type="text">
					<div class="col-4-12 block-center">
						<h5 class="bg-1 padded text-center">This is a form!</h5>
						<div class="pad-3 border-col border-bottom">
							<label class="row">
								<span class="col-3-7">Extraterrestrial ID</span>
								<input class="col-3-17">
							</label>
							<label class="row">
								<span class="col-3-7">Planet of Birth</span>
								<input class="col-3-17">
							</label>
							<label class="row">
								<span class="col-3-7">Race</span>
								<select class="col-3-17">
									<option selected disabled>- Choose -</option>
									<option>Plutonian</option>
									<option>Pleiadese</option>
									<option>Distantic</option>
								</select>
							</label>
							<button class="block push-top-4">Submit!</button>
						</div>
					</div>
				</section>
				<section class="pad-col-3">
					<h3>The Grid:</h3>
					<div class="container-6 text-center">
						<div class="row pad-row-2">
							<div class="col-1-24">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-2-12">column</div>
							<div class="col-2-12">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-3-8">column</div>
							<div class="col-3-8">column</div>
							<div class="col-3-8">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-4-6">column</div>
							<div class="col-4-6">column</div>
							<div class="col-4-6">column</div>
							<div class="col-4-6">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-5-4">column</div>
							<div class="col-5-4">column</div>
							<div class="col-5-4">column</div>
							<div class="col-5-4">column</div>
							<div class="col-5-4">column</div>
							<div class="col-5-4">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
							<div class="col-6-3">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
							<div class="col-7-2">column</div>
						</div>
						<div class="row pad-row-2">
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
							<div class="col-8-1">column</div>
						</div>
					</div>
				</section>
				<section class="pad-col-3">
					<h3>Other Elements:</h3>
					<a>Here's an a tag</a>
					<p class="strong">p.strong</p>
					<p class="em">p.em</p>
					<p>a <strong>strong</strong> tag</p>
					<p>an <em>em</em> tag</p>
					<p>An unordered list:</p>
					<ul>
						<li>List Item 1</li>
						<li>List Item 2</li>
						<li>List Item 3</li>
					</ul>
					<p>A styled unordered list:</p>
					<ul class="styled">
						<li>List Item 1</li>
						<li>List Item 2</li>
						<li>List Item 3</li>
					</ul>
					<p>An ordered list:</p>
					<ol class="styled">
						<li>List Item 1</li>
						<li>List Item 2</li>
						<li>List Item 3</li>
					</ol>
				</section>
			</div>
		</main>
		<footer class="row content-middle bg-3 text-center">
			<div class="col-3-9 pad-row-4 pad-col-2">
				<h5 class="bare">Contact Us!</h5>
				<input placeholder="email address">
				<textarea placeholder="message"></textarea>
			</div>
			<div class="col-3-6 screen-3 pad-row-4 pad-col-2">
				I am a vertically centered column and am 2/3rds the size of these columns on either side of me. I will also be hidden when we all collapse into rows.
			</div>
			<ul class="col-3-9 pad-row-4 pad-col-2">
				<li><a class="pad-row-1" href="#">Terms &amp; Conditions</a></li>
				<li><a class="pad-row-1" href="#">Privacy Policy</a></li>
				<li><a class="pad-row-1" href="#">Contact us</a></li>
			</ul>
		</footer>
		<?php
	}
}
