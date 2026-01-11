    <!-- Footer -->
    <footer class="text-center text-lg-start text-white bg-dark">
        <!-- Section: Links  -->
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <!-- Grid row -->
                <div class="row mt-3">
                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                        <!-- Content -->
                        <img draggable="false" class="mt-2 mb-3" width="45"
                            src="{{ asset('images/icons/footer_icon.png') }}" alt="logo">

                        <p>
                            <span class="text-capitalize">thanks</span> for visiting! hope to see you again soon. <span
                                class="text-capitalize">g</span>oodbye!
                        </p>

                        

                    </div>
                    <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                        <!-- Links -->
                        <h4 class="heading text-capitalize mt-3 mb-3 heading_underLine">links</h4>

                        <a class="nav-link" href="#home">
                            <h5 class="text-light text-capitalize">home</h5>
                        </a>
                        <a class="nav-link" href="#projects">
                            <h5 class="text-light text-capitalize">projects</h5>
                        </a>
                        <a class="nav-link" href="#certificates">
                            <h5 class="text-light text-capitalize">certificates</h5>
                        </a>
                        <a class="nav-link" href="#contact">
                            <h5 class="text-light text-capitalize">contact</h5>
                        </a>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <!-- Links -->
                        <h4 class="heading text-capitalize mt-3 mb-3 heading_underLine">projects</h4>
                           @foreach ($projects as $project_name)
                             
                            <a href="#projects" class="nav-link">
                            <h5 class="text-light text-capitalize">{{$project_name->project_name}}</h5></a>
                        
                           @endforeach

                    </div>
                    <!-- Grid column -->
                    <!-- Grid column -->
                    <div class="contact_link_cn col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <!-- Links -->
                        <h4 class="heading text-capitalize mt-3 mb-3 heading_underLine">contact</h4>
                        <div class="d-flex flex-column">
                           <a target="_blank" href="https://maps.app.goo.gl/2wUV14bKNHQfuem2A?g_st=aw" class="text-capitalize mb-1 text-white">marrakech, 40050, morocco</a>
                           <a href="mailto:contact@driouechoussa.com" class="mt-3 text-white">contact@driouechoussa.com</a>
                           <a href="tel:212603427204" class="mt-3 text-white">+212 603-427204</a>
                        </div>
                    </div>
                    <!-- Grid column -->
                </div>
                <!-- Grid row -->
            </div>
        </section>
        <!-- Section: Links  -->

        <!-- Copyright -->
        <div class="text-center p-3 mt-5">
            © {{date('Y')}} Oussama Driouech. All rights reserved
        </div>
    </footer>