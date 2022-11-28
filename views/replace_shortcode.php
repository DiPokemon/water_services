                <div class="services_wrapper services_wrapper-slider">
                  <?php foreach ( self::$model->get_list() as $item ): ?>
                    <div class="service_item">
                        <div class="services_column half_column">
                            <img loading="lazy" src="<?= self::$model->get_image_attachment_filepath($item->image_attachment_id) ?>" alt="<?= $item->alt ?>" title="<?= $item->img_title ?>">
                        </div>  
                        <div class="services_column half_column">
                            <h2><?= $item->title?></h2>
                            <?= $item->description ?>
                        </div> 
                    </div>    
                  <?php endforeach ?>
                </div>  