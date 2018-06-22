
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN""https://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="../css/font-awesome.min.css">
<link rel="stylesheet" href="../css/homepage.css">
<script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
<title>ParcelHub</title>
</head>
<body style='margin: 0; padding: 0;'>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full header" style="position:fixed; top:0px;">
    <tbody><tr>
        <td width="100%" valign="top" align="center" bgcolor="#ffffff">
        
            <table width="1000" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                <tbody><tr>
                    <td width="100%" align="center">
                    
                        <!-- Start Nav -->
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" height="5"></td>
                            </tr>
                            <tr>
                                <td width="100%" valign="middle" align="center">
                                    
                                    <!-- Logo -->
                                    <table width="150" border="0" cellpadding="0" cellspacing="0" align="left" class="fullCenter" style="text-align: left;">
                                        <tbody><tr>
                                            <td height="65" valign="middle" width="100%" class="fullCenter">
                                                <a href="#home"><img width="113" src="images/logo.png" alt="" border="0"></a>
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Nav --> 
                                    <table id="menu-list" width="800" border="0" cellpadding="0" cellspacing="0" align="right" style="text-align: right; font-size: 13px; letter-spacing: 1px;" class="fullCenter">    
                                        <tbody><tr>
                                            <td height="65" valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                <a href="#home" class="active" style="text-decoration: none; color: #191919;">HOME</a>
                                            </td>
                                            <td valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                <a href="#service" style="text-decoration: none; color: #191919;">SERVICE</a>
                                            </td>
                                            <td valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                <a href="#pricing" style="text-decoration: none; color: #191919;">PRICING</a>
                                            </td>
                                            <td valign="middle" width="8%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                <a href="#faq" style="text-decoration: none; color: #191919;">FAQ</a>
                                            </td>
                                            @if(auth()->check())
                                                <td valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                    <a href="@role('admin'){{ url('/dashboard') }}@endrole 
                                                        @role('user'){{ url('/lots') }}@endrole" style="text-decoration: none; color: #191919;">
                                                        @role('admin')
                                                            ADMIN PANEL
                                                        @endrole
                                                        @role('user')
                                                            MY ACCOUNT
                                                        @endrole
                                                    </a>
                                                </td>
                                            @else
                                                <td valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                    <a href="{{ url('/register') }}" style="text-decoration: none; color: #191919;">REGISTER</a>
                                                </td>
                                                <td valign="middle" width="10%" style="font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;">
                                                    <a href="{{ url('/login') }}" style="text-decoration: none; color: #191919;">LOGIN</a>
                                                </td>
                                            @endif
                                            
                                        </tr>
                                    </tbody></table>
                                                                    
                                </td>
                            </tr>
                        </tbody></table><!-- End Nav -->
                        
                    </td>
                </tr>
            </tbody></table>
            
        </td>
    </tr>
</table>

<table id="home" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
    <tbody><tr>
        <td align="center" bgcolor="#303030" style="background-image: url('images/home-1.jpg'); background-position: center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-attachment: fixed; background-size: cover; background-size: 100% auto;" id="animation" class="headerBG">
        <!--[if gte mso 9]> <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;"><v:fill type="tile" src="images/header_bg2.jpg"/><v:textbox style="mso-fit-shape-to-text:true" inset="0,0,0,0"><![endif]--><div>
        
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" id="mobileView">
                <tbody><tr>
                    <td width="100%" align="center">
                        
                        <!-- Start Header Text -->
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody>
                            <tr>
                                <td width="100%" valign="middle" align="center">
                                    
                                    <!-- Header Text --> 
                                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="right" style="text-align: center;" class="fullCenter"> 
                                        <tbody><tr>
                                            <td valign="middle" width="100%" style="text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 700; font-size: 33px; color: #ffffff; line-height: 38px;">
                                                The best logistics solutions for online to offline commerce starts here
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="12"></td>
                                        </tr>
                                        <tr>
                                            <td valign="middle" width="100%" style="text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; font-size: 15px; color: #ffffff;">
                                                Pack, Ship, and Manage your parcels all in one place at ParcelHub. Whether you are an e-seller just starting out or a SME looking to grow your business, we are here to make the experience pleasant for you.
                                            </td>
                                        </tr>
                                        <!-- End Button Center -->
                                    </tbody></table>
                                                                    
                                </td>
                            </tr>
                            <tr>
                                <td width="100%" height="150"></td>
                            </tr>
                        </tbody></table><!-- End Header Text -->
                        
                    </td>
                </tr>
            </tbody></table>
            </div><!--[if gte mso 9.]>
            </v:textbox>
            </v:fill></v:rect>
            <![endif]-->
            
        </td>
    </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" id="service">
    <tbody><tr>
        <td width="100%" valign="top" bgcolor="#f6f6f6">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                <tbody><tr>
                    <td align="center">
                        
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                            <tbody><tr>
                                <td width="100%" align="center">
                                    
                                    <!-- Headline Header -->
                                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
                                        <tbody><tr>
                                            <td width="100%" height="35"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top; text-transform: uppercase;" class="fullCenter">  
                                                OUR SERVICES                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="40"></td>
                                        </tr>
                                    </tbody></table><!-- End Headline Header -->
                                    
                                    <!-- Round Icon 1 -->
                                    <table width="150" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; text-align: center;" class="fullCenter">
                                        <tbody><tr>
                                            <td width="150">
                                                <table width="150" border="0" cellpadding="0" cellspacing="0" align="center" class="smallIcon">
                                                    <tbody><tr>
                                                        <td width="100%" height="150" bgcolor="#ffffff" style="border-radius: 100%; text-align: center;">
                                                            <i class="fa fa-archive fa-4x" aria-hidden="true" style="color:#828282"></i>
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="25"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600;line-height: 24px; vertical-align: top;"> 
                                                Storage and Warehousing               
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">    
                                                Cost effective, hassle-free storage and management for your stocks.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30">                                   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="22">                                   
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <table width="75" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
                                        <tbody><tr>
                                            <td width="100%" height="40">                                   
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Round Icon 2 -->
                                    <table width="150" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; text-align: center;" class="fullCenter">
                                        <tbody><tr>
                                            <td width="150">
                                                <table width="150" border="0" cellpadding="0" cellspacing="0" align="center" class="smallIcon">
                                                    <tbody><tr>
                                                        <td width="100%" height="150" bgcolor="#ffffff" style="border-radius: 100%; text-align: center;">
                                                            <i class="fa fa-handshake-o fa-4x" aria-hidden="true" style="color:#828282"></i>
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="25"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;">    
                                                Fulfillment Services
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">    
                                                Comprehensive inventory management – from picking and packing to receiving and handling – to achieve highest business satisfaction.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30">                                   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="22">                                   
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <table width="1" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
                                        <tbody><tr>
                                            <td width="100%" height="40">                                   
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Round Image 3 -->
                                    <table width="150" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; text-align: center;" class="fullCenter">
                                        <tbody><tr>
                                            <td width="150">
                                                <table width="150" border="0" cellpadding="0" cellspacing="0" align="center" class="smallIcon">
                                                    <tbody><tr>
                                                        <td width="100%" height="150" bgcolor="#ffffff" style="border-radius: 100%; text-align: center;">
                                                            <i class="fa fa-cog fa-4x" aria-hidden="true" style="color:#828282"></i>
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="25"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;">    
                                                Customised Logistics Solutions                    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">    
                                                Flexible logistics solutions for every customer through our affiliation with various courier and shipping partners.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30">                                   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="22">                                   
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                        </tbody></table>
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" height="40"></td>
                            </tr>
                        </tbody></table>
                    
                    </td>
                </tr>
            </tbody></table><!-- Nav Wrapper -->
        
        </td>
    </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" id="pricing">
    <tbody><tr>
        <td width="100%" valign="top" align="center" bgcolor="#ffffff">
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                <tbody><tr>
                    <td align="center">
                        
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" align="center">
                    
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                                        <tbody>
                                        <tr>
                                            <td width="100%" height="35"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top; text-transform: uppercase;" class="fullCenter">  
                                                OUR PRICING                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="40"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 1 -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Left">
                                        <tbody><tr>
                                            <td width="100%">
                                                <img src="images/storagewarehouse.jpg" alt="" border="0" width="262" height="auto" style="width: 262px; height: auto;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="0" class="h25"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 1 Text -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Right">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;">  
                                                Storage & Warehouse                      
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                Price starts from RM 15/m3 chargeable per month*. Customisable plans available according to your business needs. Save cost by paying only for what you use.
                                                </br><span style="font-size:10px;">*Terms and conditions apply.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td width="100%" align="center">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                                        <tbody>
                                        <tr>
                                            <td width="100%" height="35"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 1 -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Left">
                                        <tbody><tr>
                                            <td width="100%">
                                                <img src="images/fulfillment.jpg" alt="" border="0" width="262" height="auto" style="width: 262px; height: auto;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="0" class="h25"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 2 Text -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Right">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;">  
                                                Fulfillment                      
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                Fulfillment management fee at only RM 500/month. Enjoy an all-inclusive inventory, logistics and administrations services for your business all at one affordable price.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td width="100%" align="center">
                    
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                                        <tbody>
                                        <tr>
                                            <td width="100%" height="35"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 1 -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Left">
                                        <tbody><tr>
                                            <td width="100%">
                                                <img src="images/row-3.jpg" alt="" border="0" width="262" height="auto" style="width: 262px; height: auto;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="0" class="h25"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Image 2 Text -->
                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Right">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;">  
                                                Pack & Ship                      
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                Flexible and affordable logistics fee according to your needs. Pay only as per your requirements via logistics solutions tailored for every unique customer through our affiliation with various courier and shipping partners.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                        </tbody></table>
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" height="50"></td>
                            </tr>
                        </tbody></table>
                    
                    </td>
                </tr>
            </tbody></table>
        
        </td>
    </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" id="faq">
    <tbody><tr>
        <td width="100%" valign="top" bgcolor="#f6f6f6">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                <tbody><tr>
                    <td align="center">
                        
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                            <tbody><tr>
                                <td width="100%" align="center">
                                    
                                    <!-- Headline Header -->
                                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
                                        <tbody><tr>
                                            <td width="100%" height="35"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top; text-transform: uppercase;" class="fullCenter">  
                                                FAQ                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="40"></td>
                                        </tr>
                                    </tbody></table><!-- End Headline Header -->
                                    
                                    {{-- <table width="600" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Right">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height:24px; vertical-align: top;">   
                                                About Us                        
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                <b>ParcelHub</b> is a team dedicated to deliver the best solutions for end to end services covering from packing and shipping, to warehousing and e-commerce fulfillment </br> — all at the price of our customers’ best interest and satisfaction.
                                            </td>
                                        </tr>
                                    </tbody></table> --}}
                                    
                                    <!-- Image 2 Text -->
                                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="table50Left">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 18px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height:24px; vertical-align: top;">   
                                                Why Us?                        
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                <i class="fa fa-check" aria-hidden="true"></i> One-Stop business service centre for all your e-commerce needs.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                <i class="fa fa-check" aria-hidden="true"></i> Customisable and Flexible solutions available to cater to each customer’s business plans and needs.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 13px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 24px; vertical-align: top;">  
                                                <i class="fa fa-check" aria-hidden="true"></i> Affordable rates for the best services by our team of competent personnel.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="30"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                        </tbody></table>
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" height="40"></td>
                            </tr>
                        </tbody></table>
                    
                    </td>
                </tr>
            </tbody></table><!-- Nav Wrapper -->
        
        </td>
    </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
    <tbody><tr>
        <td width="100%" valign="top" bgcolor="#ffffff" align="center">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                <tbody><tr>
                    <td align="center">
                        
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                            <tbody><tr>
                                <td width="100%" align="center">
                    
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full">
                                        <tbody><tr>
                                            <td width="100%" height="60"></td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Footer Left -->
                                    <table width="290" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 14px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;" class="fullCenter">   
                                                About Us                        
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10">                                   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 12px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 20px; vertical-align: top;" class="fullCenter">   
                                                <b>ParcelHub</b> is a team dedicated to deliver the best solutions for end to end services covering from packing and shipping, to warehousing and </br>e-commerce fulfillment </br> — all at the price of our customers’ best interest and satisfaction.
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                    <!-- Footer Right -->
                                    <table width="290" border="0" cellpadding="0" cellspacing="0" align="right" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                        <tbody><tr>
                                            <td width="100%" style="font-size: 14px; color: #191919; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 600; line-height: 24px; vertical-align: top;" class="fullCenter">   
                                                Contact Us                  
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10">                                   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" style="font-size: 12px; color: #969696; text-align: left; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 20px; vertical-align: top;" class="fullCenter">   
                                                <b>How can we help you? Talk to us at</b>
                                                <br>
                                                <a href="mailto:parcelhub.hq@gmail.com">percelhub.hq@gmail.com</a>
                                                <br>
                                                <a href="tel:+60182991234">018 299 1234</a>
                                                <br><br>
                                                No. 26, Jalan PJU 3/49,
                                                Sunway Damansara 47810 Petaling Jaya,
                                                Selangor Darul Ehsan, Malaysia.</br>
                                                <b>Operating hours:</b>
                                                <br>
                                                Mon-Fri
                                                <br>
                                                9 AM – 6 PM

                                            </td>
                                        </tr>
                                    </tbody></table>
                                    
                                </td>
                            </tr>
                        </tbody></table>
                        
                        <!-- CopyRight -->
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                            <tbody><tr>
                                <td width="100%" height="30"></td>
                            </tr>
                            <tr>
                                <td width="100%" style="font-size: 12px; color: #969696; text-align: center; font-family: Helvetica, Arial, sans-serif, 'Open Sans'; font-weight: 400; line-height: 20px; vertical-align: top;" class="fullCenter"> 
                                    Copyright © ParcelHub 2018, All rights reserved.
                                </td>
                            </tr>
                        </tbody></table>
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="full">
                            <tbody><tr>
                                <td width="100%" height="30"></td>
                            </tr>
                            <tr>
                                <td width="100%" height="1"></td>
                            </tr>
                        </tbody></table>
                    
                    </td>
                </tr>
            </tbody></table>
        
        </td>
    </tr>
</table>
</body>
</html>

<script>
$(document).ready(function(){
  // Add smooth scrolling to all links
  $("a").on('click', function(event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;
      
      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      var test = $(hash).offset().top - 50;
      $('html body').animate({
        scrollTop: test
      }, 800, function(){
   
        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });
});

$(document).ready(function () {
    $(document).on("scroll", onScroll);
    
    //smoothscroll
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        
        $('a').each(function () {
            $(this).removeClass('active');
        })
        $(this).addClass('active');
      
        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - 50
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });
});

function onScroll(event){
    var scrollPos = $(document).scrollTop();
    $('#menu-list a[href^="#"').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if ((refElement.position().top - 80) <= scrollPos && (refElement.position().top - 80 + refElement.height()) > scrollPos) {
            $('#menu-list tbody tr td a').removeClass("active");
            currLink.addClass("active");
        }
        else{
            currLink.removeClass("active");
        }
    });
}


</script>
