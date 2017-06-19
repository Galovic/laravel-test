@extends('theme::layouts.main')

@section('content')

    <!-- BREADCRUMB -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="#">Úvodní strana</a></li>
                    <li><a href="#">Šablona</a></li>
                </ol>
            </div>
        </div>
    </div>
    <!-- /BREADCRUMB -->

    <!-- CONTAIN -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>
                    <small>Simplo.cz</small>
                </h2>
                <h1>Business Casual</h1>
                <h2 class="intro-text">Webová stránka <strong>s vysokou</strong> návštěvností</h2>
                <p>The boxes used in this template are nested inbetween a normal Bootstrap row and the start of your column
                    layout. The boxes will be full-width boxes, so if you want to make them smaller then you will need to
                    customize.</p>
                <p>A huge thanks to <a href="http://join.deathtothestockphoto.com/">Death to the Stock Photo</a> for
                    allowing us to use the beautiful photos that make this template really come to life. When using this
                    template, make sure your photos are decent. Also make sure that the file size on your photos is kept to
                    a minumum to keep load times to a minimum.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc placerat diam quis nisl vestibulum
                    dignissim. In hac habitasse platea dictumst. Interdum et malesuada fames ac ante ipsum primis in
                    faucibus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis
                    egestas.</p>
            </div>
            <div class="col-md-12 text-center">
                <h2><strong>Moderní</strong> galerie</h2>
                <div class="my-gallery" itemscope itemtype="http://schema.org/ImageGallery">
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="contentUrl" data-size="1024x1024">
                            <img src="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="contentUrl" data-size="964x1024">
                            <img src="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="contentUrl" data-size="1024x683">
                            <img src="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm6.staticflickr.com/5023/5578283926_822e5e5791_b.jpg" itemprop="contentUrl" data-size="1024x768">
                            <img src="https://farm6.staticflickr.com/5023/5578283926_822e5e5791_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm6.staticflickr.com/5023/5578283926_822e5e5791_b.jpg" itemprop="contentUrl" data-size="1024x768">
                            <img src="https://farm6.staticflickr.com/5023/5578283926_822e5e5791_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="contentUrl" data-size="1024x683">
                            <img src="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="contentUrl" data-size="964x1024">
                            <img src="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="contentUrl" data-size="1024x1024">
                            <img src="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="contentUrl" data-size="1024x1024">
                            <img src="https://farm3.staticflickr.com/2567/5697107145_a4c2eaa0cd_o.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="contentUrl" data-size="964x1024">
                            <img src="https://farm2.staticflickr.com/1043/5186867718_06b2e9e551_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                        <a href="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="contentUrl" data-size="1024x683">
                            <img src="https://farm7.staticflickr.com/6175/6176698785_7dee72237e_b.jpg" itemprop="thumbnail" alt="Popis obrázku" />
                        </a>
                        <figcaption itemprop="caption description">Popis obrázku</figcaption>
                    </figure>
                </div>

            </div>
            <div class="col-md-12 text-center">
                <h2>Responzivní <strong>carousel</strong></h2>
                <div class="jcarousel-wrapper">
                    <div class="jcarousel">
                        <ul>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img1.jpg" alt="Obrázek 1" /></li>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img2.jpg" alt="Obrázek 2" /></li>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img3.jpg" alt="Obrázek 3" /></li>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img4.jpg" alt="Obrázek 4" /></li>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img5.jpg" alt="Obrázek 5" /></li>
                            <li><img src="http://sorgalla.com/jcarousel/examples/_shared/img/img6.jpg" alt="Obrázek 6" /></li>
                        </ul>
                    </div>
                    <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                    <a href="#" class="jcarousel-control-next">&rsaquo;</a>
                </div>
            </div>
            <div class="col-md-12">
                <h2 class="text-center">Formuláře</h2>
                <form>
                    <div class="form-group">
                        <label for="inputEmail">E-mailová adresa</label>
                        <input type="email" class="form-control" id="inputEmail" placeholder="petr@novak.cz">
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="inputName">Jméno</label>
                            <input type="text" class="form-control" id="inputName" placeholder="Petr">
                        </div>
                        <div class="col-md-6">
                            <label for="inputSurname">Příjmení</label>
                            <input type="text" class="form-control" id="inputSurname" placeholder="Novák">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default btn-lg pull-right">Odeslat</button>
                </form>
            </div>

            <div class="col-md-12">
                @include('theme::articles.list')
            </div>
        </div>
    </div>
    <!-- /CONTAIN -->

    <!-- PAGINATION -->
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li>
                            <a href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li>
                            <a href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- /PAGINATION -->

@endsection