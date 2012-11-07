<?php 
//echo 'style="width:'.print $column['width'].'%'"

$i = 0;

foreach((array) $table as $i => $column) : 

	$colClass = 'radium-price-column'; $n = $i + 1;
	// column classes
	$colClass .= ( $n % 2 ) ?  '' : ' even-column';

	?>
	
<div class="radium-price-column <?php print implode(' ', $column['classes']) ?> <?php print $colClass ?>" >

	<div class="radium-price-column-inner">
			
		<div class="pricetable-column-inner">
		
			<div class="pricetable-header">
				<h3 class="column-title"><?php print $column['title'] ?></h3>
				<div class="price-info">
					<div class="cost"><?php print $column['price'] ?></div>
					<div class="details"><?php print $column['detail'] ?></div>
				</div>
			</div>
			
			<div class="features">
				<?php if(!empty($column['features'])) : ?>
					<?php foreach($column['features'] as $j => $feature) : ?>
						<div class="pricetable-feature <?php print $j == 0 ? 'pricetable-first' : '' ?>">
							<?php print $feature['title'] ?>
							<?php if(!empty($feature['sub'])) : ?>
								<small><?php print $feature['sub'] ?></small>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			
			<div class="pricetable-button-container">
				<a href="<?php print empty($column['url']) ? '#' : $column['url'] ?>" class="btn signup"><?php print empty($column['button']) ? __('Select', 'radium') : $column['button'] ?></a>
			</div>	
			<div class="last-divider"></div>			
		</div>
		
	</div>
	
</div>

<?php $i++; endforeach; ?>
		