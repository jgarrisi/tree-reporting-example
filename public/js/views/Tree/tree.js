jQuery(document).ready(function ($) {
    $('#goButton').click(function (event) {
        var APP = $('#textAPPID').val();
        // console.log("go button clicked. APP=" + APP);
        getTreeDiagramForAPP(APP);
    });
    $('#selAPPID').change(function (event) {
        var APP = $('#selAPPID').val();
        // console.log("dropdown field changed APP=" + APP);

        getTreeDiagramForAPP(APP);
    });

    loadDropDown();

    $('#selAPPID').select2();




    var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    y = w.innerHeight || e.clientHeight|| g.clientHeight;

    var margin = {top: 0, right: 0, bottom: 0, left: 200},
    width = x - margin.right - margin.left,
    height = y - margin.top - margin.bottom;

    console.log("x="+x+"; y="+y);


    var i = 0,
    duration = 750,
    root;

    var tree = d3.layout.tree()
    .size([height-100, width]);

    var diagonal = d3.svg.diagonal()
    .projection(function (d) {
        return [d.y, d.x];
    });

    var svg = d3.select("#graph").append("svg")
        //.attr("width", width + margin.right + margin.left)
        //.attr("height", height + margin.top + margin.bottom)
        //responsive SVG needs these 2 attributes and no width and height attr
        .attr("preserveAspectRatio", "xMinYMin meet")
        .attr("viewBox", "0 0 "+(x)+" "+(y)+"")
        .attr('id', 'svg')
        //class to make it responsive
        .classed("svg-content-responsive", true)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


        svg.append("legend");

    //make legend

    var colors = {
        'Not Scheduled':'#5B9BD5',
        'Scheduled':'#000000',
        'Complete':'#00B050',
        'Out of Scope':'#767171'
    };


    var legendRectSize = 125;
    var legendSpacing = 4;
    var legendcircleradius = 7;

    var legend = svg.selectAll('.legend')
    .data(Object.keys(colors))
    .enter()
    .append('g')
    .attr('class', 'legend')
    .attr('transform', function(d, i) {
        var h = legendRectSize + legendSpacing;
        var offset =  h * Object.keys(colors).length / 2;
        var horz = i*h;
            var vert = height*0.90;//i * h;// + offset;
            return 'translate(' + horz + ',' + vert + ')';
        });

    legend.append('circle')
    .attr('cx', legendRectSize  - legendSpacing )
    .attr('cy', - 5)
    .attr("r", legendcircleradius)
    .style('fill', function(d) { return colors[d]; });

    legend.append('text')
    .attr('x', legendRectSize + legendSpacing)
    .attr('y', 0)
    .text(function(d) { return d; });

    //end legend


    function getTreeDiagramForAPP(input) {
        var token = $('input[name=_token]').val();
        $.ajax({
            url: 'tree/fetchTree',
            type: "GET",
            data: {textAPPID: input, _token: token},
            success: function (data) {
                root = JSON.parse(data);
                $('#APPtitle').text(root['commonName']);
                root.x0 = height / 2;
                root.y0 = 0;

                function collapse(d) {
                    if (d.children) {
                        d._children = d.children;
                        d._children.forEach(collapse);
                        d.children = null;
                    }
                }

                root.children.forEach(collapse);
                update(root);
                updateSummary(root);
                $('#refreshInfo').text('Tree Database | '+new Date());

            },
            error: function (message) {
                console.log(message);
            }
        });

    }

    function updateSummary(data){

        console.log(data);
        var summaryBox = $('#summary-box').show();
        summaryBox.empty();

        var HPQ = {
            "Production":0,
            "Staging":0,
            "Test":0,
            "Development":0
        };
        var HPI = {
            "Production":0,
            "Staging":0,
            "Test":0,
            "Development":0
        };
        var HPE = {
            "Production":0,
            "Staging":0,
            "Test":0,
            "Development":0
        };

        var existsHPQ = false,
            existsHPE = false,
            existsHPI = false;

        //create title
        summaryBox.append(
            // $('<h4 />').text(data['commonName']+" Summary")
            $('<h4 />').addClass('text-center').text("Summary")
            );

        //create APP-ID list
        $('<b />').text("APP-ID's").appendTo('#summary-box');
        $('<ul />').attr('id','listAPPID').appendTo('#summary-box');

        data['children'].forEach(function(instance){
            $('<li />').text(instance['name']+": "+instance['APPID']).appendTo('#listAPPID');



            console.log(instance);
            instance['_children'].forEach(function(environment){
                if(instance['name']=='HPQ'){
                    HPQ[environment['name']] += environment['serverCount'];
                    existsHPQ = true;
                }else if(instance['name']=='HPE'){
                    HPE[environment['name']] += environment['serverCount'];
                    existsHPE = true;
                }else if(instance['name']=='HPI'){
                    HPI[environment['name']] += environment['serverCount'];
                    existsHPI = true;
                }else{
                    //do nothing
                }
            });

        });

        //print server counts
        if(existsHPE){
            $('<b />').text("HPE Environments").appendTo('#summary-box');
            $('<ul />').attr('id','listHPEEnvironments').appendTo('#summary-box');
            $.each(HPE,function(i,v){
                if(v!=0){
                    $('<li />').text(i+" ("+v+" servers)").appendTo('#listHPEEnvironments');
                }
            });
        }
        if(existsHPI){
            $('<b />').text("HPI Environments").appendTo('#summary-box');
            $('<ul />').attr('id','listHPIEnvironments').appendTo('#summary-box');
            $.each(HPI,function(i,v){
                if(v!=0){
                    $('<li />').text(i+" ("+v+" servers)").appendTo('#listHPIEnvironments');
                }
            });
        }
        if(existsHPQ){
            $('<b />').text("HPQ Environments").appendTo('#summary-box');
            $('<ul />').attr('id','listHPQEnvironments').appendTo('#summary-box');
            $.each(HPQ,function(i,v){
                if(v!=0){
                    $('<li />').text(i+" ("+v+" servers)").appendTo('#listHPQEnvironments');
                }
            });
        }

        $('<b />').text("Re-Address Status").appendTo('#summary-box');
        $('<ul />').attr('id','listReAddressStatus').appendTo('#summary-box');
        $.each(data['statusCounts'], function(status, count){
            if(status!='total'){
                var percent = count*100/data['statusCounts'].total;
                $('<li />').text(percent.toFixed(0)+"% "+status).appendTo('#listReAddressStatus');
            }
        });

    }

    function update(source) {
        $(tree).show();
        // Compute the new tree layout.
        var nodes = tree.nodes(root).reverse(),
        links = tree.links(nodes);

        // Normalize for fixed-depth.
        nodes.forEach(function (d) {
            d.y = d.depth * width/4;
        });

        // Update the nodes…
        var node = svg.selectAll("g.node")
        .data(nodes, function (d) {
            return d.id || (d.id = ++i);
        });

        // Enter any new nodes at the parent's previous position.
        var nodeEnter = node.enter().append("g")
        .attr("class", "node")
        .attr("transform", function (d) {
            return "translate(" + source.y0 + "," + source.x0 + ")";
        })
        .on("click", click);

        nodeEnter.append("circle")
        .attr("r", 1e-6)
        .style("fill", function (d) {
            return d.color ? d.color : "none";
        });

        nodeEnter.append("text")
        .attr("x", function (d) {
            return d.children || d._children ? -10 : 10;
        })
        .attr("dy", ".35em")
        .attr("text-anchor", function (d) {
            return d.children || d._children ? "end" : "start";
        })
        .text(function (d) {
            return d.APPID ? d.name + ' (' + d.APPID + ')' : d.name;
        })
        .style("fill-opacity", 1e-6);

        // Transition nodes to their new position.
        var nodeUpdate = node.transition()
        .duration(duration)
        .attr("transform", function (d) {
            return "translate(" + d.y + "," + d.x + ")";
        });

        nodeUpdate.select("circle")
        .attr("r", legendcircleradius)
        .style("fill", function (d) {
            return d.color ? d.color : "none";
        });

        nodeUpdate.select("text")
        .style("fill-opacity", 1);

        // Transition exiting nodes to the parent's new position.
        var nodeExit = node.exit().transition()
        .duration(duration)
        .attr("transform", function (d) {
            return "translate(" + source.y + "," + source.x + ")";
        })
        .remove();

        nodeExit.select("circle")
        .attr("r", 1e-6);

        nodeExit.select("text")
        .style("fill-opacity", 1e-6);

        // Update the links…
        var link = svg.selectAll("path.link")
        .data(links, function (d) {
            return d.target.id;
        });

        // Enter any new links at the parent's previous position.
        link.enter().insert("path", "g")
        .attr("class", "link")
        .attr("d", function (d) {
            var o = {x: source.x0, y: source.y0};
            return diagonal({source: o, target: o});
        });

        // Transition links to their new position.
        link.transition()
        .duration(duration)
        .attr("d", diagonal);

        // Transition exiting nodes to the parent's new position.
        link.exit().transition()
        .duration(duration)
        .attr("d", function (d) {
            var o = {x: source.x, y: source.y};
            return diagonal({source: o, target: o});
        })
        .remove();

        // Stash the old positions for transition.
        nodes.forEach(function (d) {
            d.x0 = d.x;
            d.y0 = d.y;
        });
    }

// Toggle children on click.
function click(d) {
    if (d.children) {
        d._children = d.children;
        d.children = null;
    } else {
        d.children = d._children;
        d._children = null;
    }
    update(d);
}
});

function loadDropDown() {
    var token = $('input[name=_token]')
    .val();
    $.ajax({
        url: 'tree/fetchAPPs',
        type: "GET",
        data: {
            _token: token
        },
        dataType: 'json',
        success: function (data) {
            // console.log("AJAX success...");
            console.log("Found "+data.length+" applications.");

            $('#selAPPID').empty();

            $.each(data, function (i,d) {
                // console.log(d.APPID + " : " + d.AppCIName);
                $('#selAPPID').append($('<option></option>').val(d.APPID).html(d.APPID+' - '+d.AppCommonName));
            });


        },
        error: function (message) {
            var errorMessage = '<div class="alert alert-danger">AJAX call failed: ' + message.responseText + '</div>';
            $('#messagesDiv')
            .append(errorMessage);
            console.log(message);
        }
    });
}
