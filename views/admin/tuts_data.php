<div class="st-wrapper">
    <div id="st-accordion" class="st-accordion">
        <ul>
            <li>
                <a href="#">Basic Usage (with example)<span class="st-arrow">Open or Close</span></a>
                <div class="st-content">
                    <blockquote>
                        Originally this tutorial was written by the WordPress user <strong><a target="_blank" href="http://wordpress.org/support/profile/unknownterritory20">unknownterritory20</a></strong> in the plugin support area <a href="http://wordpress.org/support/topic/content-does-not-come-up-after-i-create-the-page?replies=11#post-4222748" target="_blank">here</a> for BPGE v3.5.
                    </blockquote>
                    <p>In this tutorial, we will be creating two new pages that will show up on the BuddyPress group menus:</p>
                    <ol>
                        <li>Group Info</li>
                        <li>Group News</li>
                    </ol>
                    <h4>Preparation:</h4>

                    <p>If you do not have a BuddyPress group, create one.</p>

                    <h4>Step 1</h4>

                    <p>On the BP Groups Extras (BPGE) settings page on Allowed groups tab (in wp-admin area), you will see a list of all your groups. Click the check box next to the group you want to add these two new pages to and click "<em>Save Changes</em>".</p>

                    <h4>Step 2</h4>

                    <p><em>Note: Under "Default Set of Fields" you can create fields that will be available (by default) to add to your pages.</em></p>

                    <p>For this tutorial, I will create two sets of data,</p>
                    <ol>
                        <li>Group Info</li>
                        <li>Group News</li>
                    </ol>
                    <p><em>*** Skip to step 3 if you don’t want default fields ***</em></p>

                    <div class="sub">
                        <h4>Step 2.a</h4>

                        <p>Click "Create the Set of Fields"<br/>
                        Name: Group Owner<br/>
                        Description: you can leave this blank<br/>
                        Click the "<em>Show Fields</em>" button to the right. The Contact info field set will expand.</p>

                        <h4>Step 2.b</h4>
                        <p>Click "Add field"<br/>
                        Field Title: Name<br/>
                        Field Type: Text<br/>
                        Field Description: name of group owner.<br/>
                        Click "Add field"<br/>
                        Field Title: Email<br/>
                        Field Type: Text<br/>
                        Field Description: you can leave blank for now.<br/>
                        Click "Add field"<br/>
                        Field Title: Phone<br/>
                        Field Type: Text<br/>
                        Field Description: you can leave blank for now.</p>

                    </div>

                    <p><em>*** Repeat Step 2.a ***</em></p>

                    <div class="sub">
                        <p>Name: Events info<br/>
                        Description: you can leave this blank</p>
                    </div>

                    <p><em>*** Repeat 2.b ***</em></p>


                    <div class="sub">
                        <p>Field Title: Event Title<br/>
                        Field Type: Text<br/>
                        Field Description: you can leave blank for now.<br/>
                        Field Title: Event Date<br/>
                        Field Type: Text<br/>
                        Field Description: you can leave blank for now.<br/>
                        Field Title: Tickets Cost<br/>
                        Field Type: Text<br/>
                        Field Description: you can leave blank for now.</p>
                    </div>

                    <h4>Step 3</h4>

                    <p>Navigate to the BuddyPress group that you want to add these pages &amp; click "<em>Admin &gt; Extras</em>".</p>

                    <p><em>Note: You must be the WordPress Admin or BuddyPress group admin to make these changes to the BuddyPress group.</em></p>

                    <p>Click "<em>Add Page</em>" on the group extras navigation to create your pages.</p>

                    <p>1st page: The Group Info page</p>

                    <ul>
                        <li>Page Title : Group Info</li>
                        <li>Page Slug: group-info</li>
                        <li>Page Content : This is the Group Info page</li>
                    </ul>

                    <p>Repeat the process to add the 2nd and 3rd page</p>

                    <p>2nd: The Group News page</p>
                    <ul>
                        <li>Page Title : Group News</li>
                        <li>Page Slug: group –news</li>
                        <li>Page Content : This is the Group News page</li>
                    </ul>
                    <p><strong>Important:</strong> This page will display the link/tab to the events Info and all other page you may want to create.

                    <p>3rd: The More Stuff page</p>
                    <ul>
                        <li>Page Title : More Stuff</li>
                        <li>Page Slug: more-stuff</li>
                        <li>Page Content : This will be the link to all my other pages</li>
                    </ul>

                    <h4>Step 4</h4>

                    <div class="sub">
                        <h4>Step 4.a</h4>
                        <p>Click on the "<em>All Fields</em>" button on the group extras navigation.</p>

                        <p>Click on the drop down menu and select "<em>Group Owner</em>", then click the "<em>Import</em>" button.</p>

                        <p>Now select "<em>Events info</em>" and click the "<em>Import</em>" button to import those fields as well.</p>

                        <p><em>Note</em>: You can drag and drop fields. The order in which they are listed here, is the order in which they will appear on the page.</p>

                        <h4>Step 4.b</h4>
                        IMPORTANT Click the "<em>Edit Field"</em> button.
                        Under "<em>Should this field be displayed for public on "Group Info" page?</em>" select "<em>Display it</em>" &amp; save changes.
                    </div>

                    <p><em>*** For this tutorial, repeat this step for all fields ***</em></p>

                    <h4>Step 5</h4>

                    <div class="sub">
                        <h4>Step 5.a</h4>
                        <p>Click on the "<em>General</em>" button on the group extras navigation.</p>

                        <p>Please specify the page name, where all fields will be displayed<br/>
                        Enter: Group Info</p>
                        <p>Please specify the page name, where all custom pages will be displayed<br/>
                        Enter: More Stuff</p>
                        <p>Click "<em>Save Changes</em>"</p>

                        <h4>Step 5.b</h4>
                        <p>On the same page, enter/modify the following:</p>
                        <ul>
                            <li>Do you want to make " Group Info " page public? Everyone will see this page -&gt; Select "<em>Show it</em>"</li>
                            <li>Please choose the layout for "<em>Group Info</em>" page -&gt; Select "Plain" (field title and its data below)</li>
                        </ul>

                        <h4>Step 5.c</h4>
                        <p>Now reorder the menu so that the last three items are:</p>
                        <ul>
                            <li>Group Info</li>
                            <li>More Stuff</li>
                            <li>Admin</li>
                        </ul>
                    </div>

                    <h4>Step 6</h4>

                    <p>Click on the BuddyPress "<em>Details"</em> sub nav menu item on your BuddyPress group navigation. This menu item will always appear under the admin menu.</p>

                    <p>By selecting "<em>Display it</em>" in step 4.b, the fields will show up here, on the details page.</p>

                    <p><em>*** For this tutorial, enter this info for the following fields below ***</em></p>

                    <ul>
                        <li>Name : John Doe</li>
                        <li>Email : john@doe.com</li>
                        <li>Phone: 723-444-3242</li>
                        <li>Event Title: Music Fest 2014</li>
                        <li>Event Date: 2/3/2014</li>
                        <li>Tickets Cost : $10</li>
                    </ul>

                    <p>Click "<em>Save Changes</em>"</p>

                    <h4>Step 7 - Last Step</h4>

                    <p>Refresh your page and you should see the Group Info &amp; More Stuff in your BuddyPress group main navigation.</p>

                    <p>Click on "<em>More stuff</em>" and you will see the the Events Info page.</p>
                </div>
            </li>
            <li>
                <a href="#">Get any group page data<span class="st-arrow">Open or Close</span></a>
                <div class="st-content">
                    <p>If you want to get the content of any page in any group and display it anywhere (using php) - read this small tutorial.</p>
                    <p>Fist of all you need to know of which page you would like to display the content. You need to know the exact page ID. You can do that in several ways.</p>

                    <h4>Way #1</h4>
                    <ol>
                        <li>Go to the <a href="/wp-admin/edit.php?post_type=gpages" target="_blank">Groups Pages</a> here in WordPress admin area.</li>
                        <li>Use the seach field to find the required page.<br />
                            <strong>Remember</strong>: top level pages are actually groups names, second level are pages created in groups.</li>
                        <li>When you find the required page, click on "<em>Edit</em>" link, that appears when you click on its title, OR the title of a page.</li>
                        <li>On the next page look on the browser address bar - you will see smth like this:<br />
                            <code>example.com/wp-admin/post.php?post=385&action=edit</code></li>
                        <li>The number after the <code>post=</code> is what we are looking for - the group page ID. In our example it's <code>385</code>.</li>
                    </ol>

                    <h4>Way #2</h4>
                    <ol>
                        <li>Go the list of groups on you site on front-end.</li>
                        <li>Find the required group and go to its page.</li>
                        <li>In group navigation click on "<em>Admin</em>" link, and then its Extras submenu.</li>
                        <li>In Extras menu click <em>All pages</em> link and find there the required page.</li>
                        <li>Look on the browser address bar - you will see smth like this:<br />
                            <code>example.com/groups/next/admin/extras/pages-manage/?edit=385</code></li>
                        <li>The number after the <code>?edit=</code> is what we are looking for - the group page ID. In our example it's <code>385</code>.</li>
                    </ol>

                    <p>Now, as we know the group page we want to display elsewhere in the theme, you can use built-in <a target="_blank" href="http://codex.wordpress.org/Function_Reference/get_post">WordPress function</a> <code>get_post</code>.</p>
                    <pre><code>&lt;?php $page_data = get_post($id = 385); ?&gt;</code></pre>

                    <p>The most interesting is the content of this variable <code>$page_data</code></p>

                    <script src="https://gist.github.com/slaFFik/c5d11aac1256f9c9d3d7.js"></script>

                    <p>The description of each field can be found <a target="_blank" href="http://codex.wordpress.org/Function_Reference/get_post#Return">here</a>.</p>

                    <p>And here is the working code to display the page in your template files.</p>

                    <script src="https://gist.github.com/slaFFik/8adb5826f5ceb0cc13db.js"></script>

                    <p>Nothing difficult! Just copy and paste wherever you need the code above.</p>

                </div>
            </li>
            <li>
                <a href="#">Get any group field data<span class="st-arrow">Open or Close</span></a>
                <div class="st-content">
                    <p>If you want to get the content of any field in any group and display it anywhere (using php) - read this small tutorial.</p>

                    <p>Getting fields data is a bit more trickier, as the data is spreaded among several tables (comparing with groups pages). But WordPress API will help us to do that.</p>
                    <p>First of all you will need to know, which fields to display: all groups fields or just particular 1 field.</p>

                    <h4>All fields of a group</h4>

                    <p>To get <strong>all</strong> group fields use this function: <code>bpge_get_group_fields($status, $group_id)</code>, where:</p>
                    <ul>
                        <li><code>$status</code> are ordinary WP post statues, you may use <code>publish</code> - as it will grab those fields with <em>Display</em> enabled;<br /></li>
                        <li><code>$group_id</code> is the id of a group you want to get all fields for, just a number. You can get this infomation on <a href="/wp-admin/admin.php?page=bp-groups">this Groups</a> page (click on edit on any group and look into the browser address bar - the number after <code>gid=</code> is what you are looking for).</li>
                    </ul>

                    <p>Now we need to display them. We have several options for this, but the best advice will be for you to look into the source code.<br />
                        Open <code>/core/loader.php</code> file, find the method <code>display()</code> (it's around the line #100). Now you see everything :)
                    </p>

                    <p>Below is the example you may copy and paste into your template</p>

                    <script src="https://gist.github.com/slaFFik/8bc41c993c580954128a.js"></script>

                    <h4>Particular field of a group</h4>

                    <p>In this case you will need to get the actual ID of a field you want to display:</p>
                    <ol>
                        <li>Find the group you want to display the field from.</li>
                        <li>Go to that group admin area.</li>
                        <li>Open <em>Extras</em> page and click on <em>All Fields</em> top menu item.</li>
                        <li>Click on the required <em>Edit field</em> button.</li>
                        <li>Look in the browser address bar - the number after <code>?edit=</code> is the field ID you are looking for.</li>
                    </ol>

                    <p>Now you are ready to display its content elsewhere. Just use the code below.</p>

                    <script src="https://gist.github.com/slaFFik/23a6a00461909338ec29.js"></script>

                    <p>Hope everything is clear from the comments for almost each line of a code above.</p>

                </div>
            </li>

        </ul>
    </div>
</div>

<!-- Now load the accordion -->
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#st-accordion').accordion({
        oneOpenedItem : true
    });
});
</script>