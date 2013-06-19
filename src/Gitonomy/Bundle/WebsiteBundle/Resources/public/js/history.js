
$(document).on('ready', function () {
    $("table[data-history-graph]").each(function (i, e) {
        gitonomyHistory.inject(e);
    });
});

gitonomyHistory = {

    /**
     * Loads the history graph on a table element.
     */
    inject: function(table)
    {
        var $table = $(table);
        var data   = $.parseJSON($table.attr('data-history-graph'));

        var width      = $table.width();
        var height     = $table.height();
        var cell_size  = height / (data.length - 1);
        var cell_width = Math.floor(width/cell_size);

        var graph   = gitonomyHistory.convertToSchema(data);

        var familyColor = d3.scale.category20();
        var xAxis       = d3.scale.linear().domain([0, cell_width]).range([cell_size/2, width - cell_size/2]);

        var yComputed     = [];
        var tablePosition = null;
        var tds           = $table.find("td.message");
        var yAxis         = function (i) {
        var pos, $td;
            if (yComputed[i] !== undefined) {
                return yComputed[i];
            }

            if (i >= tds.length) {
                return yAxis(i-1) + 100;
            }

            $td = $(tds[i]);
            pos = $td.position();
            yComputed[i] = pos.top + $td.outerHeight()/2;

            return yComputed[i];
        };

        $table.wrap('<div class="history-graph-wrapper" />');

        var history = d3.select($table.parent()[0]).append('svg').attr('height', height);

        for (i in graph.heights) {
            $($table.find("td.message").eq(i)).css('padding-left', graph.heights[i] * cell_size);
        }

        $table.find("th").each(function (i, e) {
            var $cell = $(e);
            var b = $cell.parent().prev().find("td.message").css('paddingLeft');
            var a = $cell.parent().next().find("td.message").css('paddingLeft');
            var res;
            if (undefined === a) {
                res = parseInt(b);
            } else if (undefined === b) {
                res = parseInt(a);
            } else {
                res = Math.max(parseInt(a), parseInt(b));
            }

            $cell.css('padding-left', res);
        });

        var commits = history
            .selectAll('circle')
            .data(graph.nodes)
        ;

        var links = history
            .selectAll('path')
            .data(graph.links)
        ;

        var commit_line = d3.svg.line()
            .x(function (d) {
                return xAxis(d.x);
            })
            .y(function (d) {
                return yAxis(d.y);
            })
        ;


        links.enter()
            .append('path')
            .attr('d', commit_line)
            .style('stroke-linecap', 'round')
            .style('stroke', function (path) {
                return familyColor(path[0].family);
            })
            .style('stroke-width', '6')
            .attr('radius', 0.1)
            .style('fill', 'none')
        ;

        var g = commits.enter().append('g');

        g.append('circle')
            .attr('cx', function (node) {
                return xAxis(node.x);
            })
            .attr('cy', function (node) {
                return yAxis(node.y);
            })
            .attr('r', 4)
            .attr('fill', 'white')
            .style('stroke', 'black')
            .style('stroke-width', '2')
        ;
    },


    /**
     * Converts to a schema containing nodes and links.
     */
    convertToSchema: function(commits) {
        var positions = {},
            matrix    = [],
            position,
            drawn     = [],
            link,
            links = [],
            heights = []
        ;

        // Preparation
        commits.forEach(function (commit, i) {
            commits[i].position = i;
            commits[i].x = -1;
            commits[i].y = i;
            commits[i].children = [];

            positions[commit.hash] = i;
            matrix[i] = [];

            commits[i].family = null;
        });

        // Children computing
        commits.forEach(function (commit, i) {
            compute_children(i);
        });

        // Compute family
        commits.forEach(function (commit, i) {
            compute_family(commit, i);
        });

        // Draw
        commits.forEach(function (commit, i) {
            matrix_draw(i);
        });

        // Heights
        commits.forEach(function (commit, i) {
            heights[i] = matrix_hashHeight(i, "text");
        })

        function compute_children(i) {
            if (commits[i].children_spread !== undefined) {
                return;
            }
            commits[i].parents.forEach(function (parent) {
                if (positions[parent] === undefined) {
                    return;
                }

                position = positions[parent];
                commits[position].children.push(commits[i].hash);

                compute_children(position);
            });

            commits[i].children_spread = true;
        }

        function compute_family(commit, i) {
            // No parent (initial commit)
            if (commit.parents.length === 0) {
                commits[i].family = commit.hash;

                return;
            }

            var firstParent = commit.parents[0];
            var firstPos = positions[firstParent];

            if (firstPos === undefined) {
                commits[i].family = commit.hash;
            } else {
                compute_family(commits[firstPos], firstPos);
                if (commits[firstPos].children.length === 1) {
                    commits[i].family = commits[firstPos].family;
                } else if (commits[firstPos].children[0] == commit.hash) {
                    commits[i].family = commits[firstPos].family;
                } else {
                    commits[i].family = commit.hash;
                }
            }
        }


        function matrix_hashHeight(position, hash) {
            var i = 0;
            while (matrix[position][i] != hash && matrix[position][i] !== undefined) {
                i++;
            }

            matrix[position][i] = hash;

            return i;
        }

        function matrix_draw(position) {
            drawn[position] = true;
            var commit = commits[position];
            var x = matrix_hashHeight(position, commit.hash);
            var family;
            commit.x = x;

            var parents = commit.parents;

            parents.forEach(function (parent) {
                if (positions[parent] !== undefined) {

                    if (drawn[positions[parent]] === undefined) {
                        matrix_draw(positions[parent]);
                    }

                    if (commits[positions[parent]].children.length > 1) {
                        family = commits[position].family;
                    } else {
                        family = commits[positions[parent]].family;
                    }
                } else {
                    family = commits[position].family;
                }

                matrix_line(commit.hash, parent, family);
            });
        }

        function matrix_line(from, to, family) {

            var fromX = commits[positions[from]].x;
            var fromY = commits[positions[from]].y;
            var toX, toY;
            if (positions[to] !== undefined) {
                toX   = commits[positions[to]].x;
                toY   = commits[positions[to]].y;
            } else {
                toX   = null;
                toY   = commits.length;
            }

            var x,y;

            for (y = fromY; y < toY - 1; y++) {
                x = matrix_hashHeight(y + 1, to);
                links.push([
                    {x: fromX, y: y, family: family},
                    {x: x, y: y + 1}
                ]);
                fromX = x;
            }

            if (null === toX) {
                toX = fromX;
            }

            links.push([
                {x: fromX, y: y, family: family},
                {x: toX,   y: y + 1}
            ]);
        }

        return {
            nodes: commits,
            links: links,
            heights: heights
        };
    }
};
