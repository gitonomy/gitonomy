function history_graph(commits) {
    var positions = {},
        matrix    = [],
        position,
        drawn     = [],
        link,
        links = []
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
        links: links
    };
}
